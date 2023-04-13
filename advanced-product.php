<?php
/*
Plugin Name: Advanced Product
Plugin URI: https://github.com/templaza/advanced-product
Description: This plugin help you manage advanced products.
Author: Templaza
Version: 1.0.6
Text Domain: advanced-product
Domain Path:  /languages/
Author URI: http://templaza.com
Forum: https://www.templaza.com/Forums.html
Ticket: https://www.templaza.com/tz_membership/addticket.html
FanPage: https://www.facebook.com/templaza
Twitter: https://twitter.com/templazavn
Google+: https://plus.google.com/+Templaza
*/

namespace Advanced_Product;

use Advanced_Product\Helper\AP_Custom_Field_Helper;
use Advanced_Product\Helper\AP_Product_Helper;
use Advanced_Product\Helper\AP_Custom_Taxonomy_Helper;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Advanced_Product{

    protected $pages;
    protected $taxonomies;
    protected $post_types;
    protected $meta_boxes;
    protected $shortcodes;
    protected $text_domain;
    protected $field_layouts;
    protected static $instance;

    public function __construct()
    {
        require_once dirname(__FILE__).'/includes/autoloader.php';

        $this -> text_domain    = AP_Functions::get_my_text_domain();

        register_activation_hook(ADVANCED_PRODUCT . '/' . ADVANCED_PRODUCT, 'FieldHelper::add_term_order_field');
        register_activation_hook(  ADVANCED_PRODUCT . '/' . ADVANCED_PRODUCT, 'flush_rewrite_rules', 15 );

        $this -> register_post_types();
        $this->register_taxonomies();
        $this->register_custom_taxonomies();
        $this->register_pages();

        $this->register_field_layouts();
        $this->register_meta_boxes();
        $this->register_shortcodes();

        if (!class_exists('Advanced_Product_ACF_Custom') && !defined('ADVANCED_PRODUCT_ACF_LITE')) {
            define('ADVANCED_PRODUCT_ACF_LITE', true);

            // Include Advanced Custom Fields
            include_once(ADVANCED_PRODUCT_LIBRARY_PATH . '/acf_custom/acf_custom.php');
        }

        if (!class_exists('acf_options_page_plugin')) {
            include('includes/library/acf-options-page/acf-options-page.php');
        }
        if (!function_exists('acf_register_flexible_content_field')) {
            include('includes/library/acf-flexible-content/acf-flexible-content.php');
        }
        if (!function_exists('acf_register_fields')) {
            include('includes/library/acf-gallery/acf-gallery.php');
        }

        require_once ADVANCED_PRODUCT_PATH . '/includes/classes/class-acf_taxonomy_walker.php';

        // include 3rd party
        do_action('advanced-product/after_init');
    }

    public static function instance(){
        if(static::$instance){
            return static::$instance;
        }

        $instance   = new Advanced_Product();

        $instance -> hooks();

        static::$instance   = $instance;
        return $instance;
    }

    public function hooks(){
        register_activation_hook( ADVANCED_PRODUCT_PATH.'/advanced-product.php', array( $this, 'import_custom_fields' ) );

        add_filter('templaza-framework/shortcode/content_area/theme_html', array($this, 'theme_html'), 11);

        add_action( 'switch_theme', 'flush_rewrite_rules', 15 );
        add_action( 'init', array( $this, 'ap_load_plugin_textdomain' ) );

        add_action('init', array($this, 'register_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        add_action('admin_init', array($this, 'update_custom_fields'));

        // Use a custom walker for the ACF dropdowns
        // Change taxonomy id to slug
        add_filter('acf/fields/taxonomy/wp_list_categories', array($this, 'acf_wp_list_categories'), 10, 2);

        if(is_admin()){
            // Import my info when import data from templaza framework
            require_once ADVANCED_PRODUCT_CLASSES_PATH.'/class-import_sync_templaza_framework.php';
            if(class_exists('Advanced_Product\Import_Sync_Templaza_Framework')) {
                $import_sync = new Import_Sync_Templaza_Framework();
            }

            // Add options to wordpress settings
            add_action( 'admin_init', array($this, 'wordpress_settings'));
            // Save options from wordpress settings
            add_action( 'admin_init', array($this, 'save_wordpress_settings'));

            add_action( 'admin_notices', array($this, 'admin_notices'),999);
        }

        add_action('pre_get_posts', array($this, 'custom_query_vars'));

        // Replace slug
        add_filter( 'register_post_type_args', array($this, 'change_post_types_slug'), 10, 2 );
    }

    public function admin_notices(){

        session_start();
        $app    = Application::get_instance();
        $queues = $app -> get_message_queue();

        if($queues && count($queues)) {
            foreach ($queues as $notice) {
                $notice_option      = isset($notice['options'])?$notice['options']:array();
                $show_close_button  = isset($notice_option['show_close_button'])?(bool) $notice_option['show_close_button']:true;

                switch ($notice['type']){
                    default:
                        $notice_type    = $notice['type'];
                        break;
                    case 'message':
                    case 'primary':
                        $notice_type    = 'info';
                        break;
                        break;
                    case 'notice':
                        $notice_type    = 'warning';
                        break;
                }
                ?>
                <div class="notice notice-<?php echo esc_attr($notice_type);
                echo $show_close_button?' is-dismissible':''; ?>">
                    <p>
                        <b><?php _e('Advanced Products Notice', 'advanced-product');?></b><br/>
                        <?php echo $notice['message']; ?></p>
                </div>
            <?php }
        }
    }

    public function change_post_types_slug( $args, $post_type ) {

        /*item post type slug*/
        if ( 'ap_product' === $post_type ) {
            $custom_slug    = get_option('ap_archive_permalink');
            if($custom_slug) {
                $args['rewrite']['slug'] = $custom_slug;
            }
        }

        return $args;
    }

    public function import_custom_fields(){

        $imported_key   = '_advanced_product_custom_field_protected_imported';

        // Check imported
        $imported   = get_option($imported_key, 0);

        if($imported){
            return true;
        }

        $importer_file = ADVANCED_PRODUCT_LIBRARY_PATH.'/importer/class-advanced-product-importer.php';

        if ( ! class_exists( 'Advanced_Product_Importer' ) ) {
            if ( file_exists( $importer_file ) )
                require_once $importer_file;
        }

        if(!class_exists('Advanced_Product_Importer')){
            return false;
        }
        $_file  = ADVANCED_PRODUCT_PATH.'/data/custom_fields.xml';

        $importer   = new \Advanced_Product_Importer();

        ob_start();
        $importer->import($_file);
        $result = ob_get_contents();
        ob_end_clean();

        if($result){
            update_option($imported_key, 1);
        }

        return true;
    }

    public function update_custom_fields(){
        if(!post_type_exists('ap_custom_field')){
            return;
        }

        // Check product type, rental price, rental unit exists
        $ptype_exists   = AP_Custom_Field_Helper::get_custom_field('ap_product_type');
        $runit_exists   = AP_Custom_Field_Helper::get_custom_field('ap_rental_unit');
        $rprice_exists  = AP_Custom_Field_Helper::get_custom_field('ap_rental_price');
        $sold_exists    = AP_Custom_Field_Helper::get_custom_field('ap_price_sold');
        $contact_exists = AP_Custom_Field_Helper::get_custom_field('ap_price_contact');

        $importer   = false;

        if(!$ptype_exists || !$runit_exists || !$rprice_exists || !$sold_exists || !$contact_exists){
            // Require import object
            $importer_file = ADVANCED_PRODUCT_LIBRARY_PATH.'/importer/class-advanced-product-importer.php';

            if ( ! class_exists( 'Advanced_Product_Importer' ) ) {
                if ( file_exists( $importer_file ) )
                    require_once $importer_file;
            }

            if(!class_exists('Advanced_Product_Importer')){
                return;
            }

            $importer   = new \Advanced_Product_Importer();

        }

        if(!$importer){
            return;
        }

        // Import product type field
        if(!$ptype_exists){
            $file  = ADVANCED_PRODUCT_PATH.'/data/upgrade/custom-fields/product_type.xml';

            if(file_exists($file)){
                ob_start();
                $importer->import($file);
                $result = ob_get_contents();
                ob_end_clean();
            }
        }

        // Import rental price & rental unit field
        if(!$rprice_exists || !$runit_exists){
            $file  = ADVANCED_PRODUCT_PATH.'/data/upgrade/custom-fields/rental_price.xml';

            if(file_exists($file)){
                ob_start();
                $importer->import($file);
                $result = ob_get_contents();
                ob_end_clean();
            }
        }

        // Import contact field
        if(!$contact_exists){
            $file  = ADVANCED_PRODUCT_PATH.'/data/upgrade/custom-fields/price_contact.xml';

            if(file_exists($file)){
                ob_start();
                $importer->import($file);
                $result = ob_get_contents();
                ob_end_clean();
            }
        }

        // Import sold field
        if(!$sold_exists){
            $file  = ADVANCED_PRODUCT_PATH.'/data/upgrade/custom-fields/price_sold.xml';

            if(file_exists($file)){
                ob_start();
                $importer->import($file);
                $result = ob_get_contents();
                ob_end_clean();
            }
        }
    }

    /**
     * Use a custom walker for the ACF dropdowns
     * @param  array $args 		The default dropdown arguments
     * @param  array $field 	The Car Dealer field
     * @return array 			Default dropdown args + custom walker
     */
    public function acf_wp_list_categories( $args, $field ) {
        $ap_taxonomies  = array('ap_branch', 'ap_category', 'ap_group_field');

        if(($ap_custom_taxonomies = AP_Custom_Taxonomy_Helper::get_taxonomies()) && is_array($ap_custom_taxonomies)) {
            $custom_tax_slugs   = wp_list_pluck($ap_custom_taxonomies, 'post_name');
            $ap_taxonomies = array_merge($ap_taxonomies,$custom_tax_slugs);
        }

        if(in_array($args['taxonomy'], $ap_taxonomies)) {
            $args['walker'] = new ACF_Taxonomy_Walker($field);
        }
        return $args;
    }

    /**
     * L10N
     *
     * @access public
     * @return void
     */
    public function ap_load_plugin_textdomain() {
        load_plugin_textdomain( 'advanced-product', false, ADVANCED_PRODUCT_PATH . '/languages' );
        load_plugin_textdomain( 'acf', false, ADVANCED_PRODUCT_PATH . '/languages/acf/' );
    }

    public function register_pages(){
        $path   = ADVANCED_PRODUCT_PAGES_PATH;
        if(!$path || ($path && !is_dir($path))){
            return false;
        }

        $files  = glob($path.'/*.php');

        if(count($files)){
            foreach ($files as $file){
                $info = pathinfo($file);
                $file_name  = $info['filename'];

                if(!is_file($file)){
                    $file   .= '/'.$file_name.'.php';
                }

                $class_name = 'Advanced_Product\Page\\'.ucfirst(str_replace('-', '_', $file_name));

                if(file_exists($file) && !class_exists($class_name)){
                    require_once $file;
                }

                if(class_exists($class_name)){
                    $page_obj  = new $class_name($this);
                    $this -> pages[$file_name] = $page_obj;
                }
            }
        }
    }

    public function register_taxonomies(){
        $path   = ADVANCED_PRODUCT_TAXONOMIES_PATH;
        if(!$path || ($path && !is_dir($path))){
            return false;
        }

        $files  = glob($path.'/*.php');
        if(count($files)){
            foreach ($files as $file){
                $info = pathinfo($file);
                $file_name  = $info['filename'];

                if(!is_file($file)){
                    $file   .= '/'.$file_name.'.php';
                }

                $class_name = 'Advanced_Product\Taxonomy\\'.ucfirst(str_replace('-', '_', $file_name));

                if(file_exists($file) && !class_exists($class_name)){
                    require_once $file;
                }

                if(class_exists($class_name)){
                    $obj  = new $class_name($this);
                    $this -> taxonomies[$file_name] = $obj;
                }
            }
        }
    }

    public function register_post_types(){
        $path   = ADVANCED_PRODUCT_POST_TYPES_PATH;
        if(!$path || ($path && !is_dir($path))){
            return false;
        }

        $files  = glob($path.'/*.php');
        if(count($files)){
            foreach ($files as $file){
                $info = pathinfo($file);
                $file_name  = $info['filename'];

                if(!is_file($file)){
                    $file   .= '/'.$file_name.'.php';
                }

                $class_name = 'Advanced_Product\Post_Type\\'.ucfirst(str_replace('-', '_', $file_name));

                if(file_exists($file) && !class_exists($class_name)){
                    require_once $file;
                }

                if(class_exists($class_name) && !isset($this -> post_types[$file_name])){
                    $post_type_obj  = new $class_name($this);
                    $this -> post_types[$file_name] = $post_type_obj;
                }
            }
        }
    }

    public function register_custom_taxonomies(){
        $args = array(
            'order'       => 'ASC',
            'orderby'     => 'ID',
            'post_status' => 'publish',
            'post_type'   => 'ap_custom_category'
        );

        $categories = get_posts( $args );

        if(!empty($categories) && is_array($categories)){
            foreach($categories as $category){
                $slug       = get_post_meta($category -> ID, 'slug', true);
                if(!empty($slug) && !isset($this -> taxonomies[$slug])){
                    $this -> taxonomies[$slug]   = new Custom_Taxonomy($category);
                }

            }
        }
    }


    public function register_shortcodes(){
        $path   = ADVANCED_PRODUCT_CORE_PATH.'/shortcodes';
        if(!$path || ($path && !is_dir($path))){
            return false;
        }

        $files  = glob($path.'/*', GLOB_ONLYDIR);

        if(count($files)){
            foreach ($files as $file){
                $info = pathinfo($file);
                $file_name  = $info['filename'];

                if(!is_file($file)){
                    $file   .= '/'.$file_name.'.php';
                }

                $class_name = 'Advanced_Product\Shortcode\\'.ucfirst(str_replace('-', '_', $file_name));

                if(file_exists($file) && !class_exists($class_name)){
                    require_once $file;
                }

                if(!class_exists($class_name)){
                    $class_name .= 'SCAP';
                }

                if(class_exists($class_name) && !isset($this -> shortcodes[$file_name])){
                    $post_type_obj  = new $class_name($this);
                    $this -> shortcodes[$file_name] = $post_type_obj;
                }
            }
        }
    }

    public function register_meta_boxes(){
        $path   = ADVANCED_PRODUCT_CORE_PATH.'/meta-boxes';
        if(!$path || ($path && !is_dir($path))){
            return false;
        }

//        $files  = glob($path.'/*[\.php]*?');
        $files  = \glob($path.'/*');

        if(count($files)){
            foreach ($files as $file){
                $info = pathinfo($file);
                $file_name  = $info['filename'];

                if(isset($info['extension']) && $info['extension'] != 'php'){
                    continue;
                }

                if(!is_file($file)){
                    $file   .= '/'.$file_name.'.php';
                }

                $class_name = 'Advanced_Product\Meta_Box\\'.ucfirst(str_replace('-', '_', $file_name));

                if(file_exists($file) && !class_exists($class_name)){
                    require_once $file;
                }

                if(class_exists($class_name) && !isset($this -> meta_boxes[$file_name])){
                    $post_type_obj  = new $class_name($this);
                    $this -> meta_boxes[$file_name] = $post_type_obj;
                }
            }
        }
    }
    public function register_field_layouts(){

        if(stripos($_SERVER['SCRIPT_NAME'], strrchr( wp_login_url(), '/') ) !== false){
            return;
        }
        $path   = ADVANCED_PRODUCT_CORE_PATH.'/field-layouts';
        if(!$path || ($path && !is_dir($path))){
            return false;
        }

        $files  = \glob($path.'/*');

        if(count($files)){
            foreach ($files as $file){
                $info = pathinfo($file);
                $file_name  = $info['filename'];

                if(isset($info['extension']) && $info['extension'] != 'php'){
                    continue;
                }

                if(!is_file($file)){
                    $file   .= '/'.$file_name.'.php';
                }

                if(file_exists($file)){
                    require_once $file;
                }
            }
        }
    }

    public function template_include($template){

        if ( is_embed() ) {
            return $template;
        }

        $post_type  = get_post_type();

        if($post_type != 'ap_product'){
            return $template;
        }

        $plugin_path    = ADVANCED_PRODUCT_TEMPLATE_PATH;
        $theme_path     = ADVANCED_PRODUCT_THEME_TEMPLATE_PATH;
        $framework_path = ADVANCED_PRODUCT_TEMPLAZA_FRAMEWORK_TEMPLATE_PATH;

        // Is single file
        if(is_single() && is_singular($post_type) ){
            // File path from theme
            $file   = $theme_path.'/'.basename($template);

            // File path from templaza-framework
            if(!file_exists($file)){
                $file   = $framework_path.'/'.basename($template);
            }

            // File path from my plugin
            if(!file_exists($file)){
                $file   = $plugin_path.'/'.basename($template);
            }

            if(file_exists($file)){
                $template   = $file;
            }
        }

        return $template;
    }

    public function the_content($content){
        return $content;
    }

    public function theme_html($html){
        global $post_type;

        $_post_type = !empty($post_type)?$post_type:get_post_type();

        if((!is_array($_post_type) && $_post_type != 'ap_product') || (is_array($_post_type) && !in_array('ap_product', $_post_type))){
            return $html;
        }

        $plugin_path    = ADVANCED_PRODUCT_TEMPLATE_PATH;
        $theme_path     = ADVANCED_PRODUCT_THEME_TEMPLATE_PATH;
        $framework_path = ADVANCED_PRODUCT_TEMPLAZA_FRAMEWORK_TEMPLATE_PATH;

        $file_name  = '';
        // Is single file
        if(is_single() && is_singular($post_type) ){
            $file_name  = 'single';
        }elseif(is_archive()){
            $file_name  = 'archive';
            if ( !have_posts()) {
                $file_name  .= '/no_content';
            }
        }

        // File path from theme
        $file   = $theme_path.'/'.$file_name.'.php';

        // File path from templaza-framework
        if(!file_exists($file)){
            $file   = $framework_path.'/'.$file_name.'.php';
        }

        // File path from my plugin
        if(!file_exists($file)){
            $file   = $plugin_path.'/'.$file_name.'.php';
        }

        if(file_exists($file)) {
            ob_start();
            require $file;
            $html   = ob_get_contents();
            ob_end_clean();

            wp_reset_postdata();
        }

        return $html;
    }

    public function validate_page()
    {
        // global
        global $pagenow, $typenow, $post_type;

        $post_id    = isset($_REQUEST['post'])?$_REQUEST['post']:(isset($_REQUEST['post_id'])?$_REQUEST['post_id']:null);
        $_post_type = !empty($post_type)?$post_type:(isset($_REQUEST['post_type'])?sanitize_title($_REQUEST['post_type']):\get_post_type($post_id));
        $_post_type = preg_replace('/^ap_/', '', $_post_type);

        $my_post_types  = array_keys($this -> post_types);

        $return = false;

        // Validate post type
        if( in_array( $pagenow, array('edit.php', 'edit-tags.php', 'post.php', 'post-new.php', 'admin-ajax.php') ) )
        {
            if(in_array($_post_type, $my_post_types)){
                $return = true;
            }
        }

        // return
        return $return;
    }

    public function register_scripts(){
        if(is_admin()) {
            wp_register_script('advanced-product', '');
            wp_register_style('advanced-product_admin_styles',
                AP_Functions::get_my_url().'/assets/css/admin.css', array(), AP_Functions::get_my_version());
            wp_register_script('advanced-product_admin_scripts',
                AP_Functions::get_my_url().'/assets/js/admin.js', array(), AP_Functions::get_my_version());
            wp_register_script('advanced-product_admin_sanitize-title-script',
                AP_Functions::get_my_url().'/assets/js/wp-fe-sanitize-title.js', array('advanced-product'), AP_Functions::get_my_version());

            wp_enqueue_style('advanced-product_admin_styles');

        }else{
            wp_register_script('advanced-product', AP_Functions::get_my_url().'/assets/js/advanced-product.js',
                array(), AP_Functions::get_my_version(), true);
            wp_register_style('advanced-product', AP_Functions::get_my_url().'/assets/css/style.css');
        }
    }

    public function admin_enqueue_scripts($hook ){
        if($this -> validate_page()) {

            global $userdata;

            wp_enqueue_script('advanced-product');

            wp_register_script('advanced-product__js_uikit', AP_Functions::get_my_url()
                .'/assets/vendor/uikit/js/uikit.min.js');
//            wp_add_inline_script('advanced-product', 'var advanced_product = {};', '');
            wp_localize_script('advanced-product', 'advanced_product', array(
                'archive_sort_nonce' => wp_create_nonce( 'ap_archive_sort_nonce_' . $userdata->ID),
                'orderby'   => (isset($_REQUEST['orderby'])?$_REQUEST['orderby']:'menu_order')
            ));
            wp_enqueue_script('advanced-product_admin_scripts');
        }
    }

    /**
     * Add options to wordpress settings
     * */
    public function wordpress_settings() {
        add_settings_field('ap_archive_permalink', __('Advanced Product archive slug', 'advanced-product'),
            array($this, 'archive_permalink_option'), 'permalink', 'optional');
    }

    /**
     * Generate html option for wordpress settings
     * */
    public function archive_permalink_option(){
        ?>
        <input name="ap_archive_permalink" type="text" class="regular-text code" value="<?php
        echo esc_attr(get_option('ap_archive_permalink', 'ap-product')); ?>" placeholder="<?php echo 'ap-product'; ?>" />
    <?php
    }

    /**
     * Save options from wordpress settings
     * */
    public function save_wordpress_settings() {
        global $pagenow;
        if ($pagenow == 'options-permalink.php' && isset($_POST['ap_archive_permalink'])) {
            update_option('ap_archive_permalink', trim($_POST['ap_archive_permalink']));
        }
    }

    /**
     * Custom query on frontend
     * */
    public function custom_query_vars($query){
        if ( is_admin() || ! $query->is_main_query() )
            return;

        if (is_archive('ap_product')) {
            $order_opt  = get_field('ap_archive_product_order_by', 'option');
            $order_opt  = $order_opt?$order_opt:'rdate';
            $order_opt  = isset($_GET['sort_order']) && !empty($_GET['sort_order'])?$_GET['sort_order']:$order_opt;

            switch ($order_opt){
                default:
                case 'rdate':
                case 'date_high':
                    $order      = 'DESC';
                    $order_by   = 'date';
                    break;
                case 'date':
                case 'date_low':
                    $order      = 'ASC';
                    $order_by   = 'date';
                    break;
                case 'alpha':
                case 'title_low':
                    $order      = 'ASC';
                    $order_by   = 'title';
                    break;
                case 'ralpha':
                case 'title_high':
                    $order      = 'DESC';
                    $order_by   = 'title';
                    break;
                case 'author':
                    $order      = 'ASC';
                    $order_by   = 'author';
                    break;
                case 'rauthor':
                    $order      = 'DESC';
                    $order_by   = 'author';
                    break;
                case 'hits':
                    $order      = 'ASC';
                    $order_by   = 'meta_value_num';
                    $query -> query_vars['meta_key'] = 'post_views_count';
                    break;
                case 'rhits':
                    $order      = 'DESC';
                    $order_by   = 'meta_value_num';
                    $query -> query_vars['meta_key'] = 'post_views_count';
                    break;
                case 'price':
                case 'price_low':
                    $order      = 'ASC';
                    $order_by   = 'meta_value_num';
                    $query -> query_vars['meta_key'] = 'ap_price';
                    break;
                case 'rprice':
                case 'price_high':
                    $order      = 'DESC';
                    $order_by   = 'meta_value_num';
                    $query -> query_vars['meta_key'] = 'ap_price';
                    break;
            }
            $query -> query_vars['order'] = $order;
            $query -> query_vars['orderby'] = $order_by;
        }
    }
}

Advanced_Product::instance();