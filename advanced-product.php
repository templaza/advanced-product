<?php
/*
Plugin Name: Advanced Product
Plugin URI: https://github.com/templaza/advanced-product
Description: This plugin help you manage advanced products.
Author: Templaza
Version: 1.0.2
Text Domain: advanced-product
Author URI: http://templaza.com
Forum: https://www.templaza.com/Forums.html
Ticket: https://www.templaza.com/tz_membership/addticket.html
FanPage: https://www.facebook.com/templaza
Twitter: https://twitter.com/templazavn
Google+: https://plus.google.com/+Templaza
*/

namespace Advanced_Product;

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
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

//        if($this -> validate_page()) {
        add_action('init', array($this, 'register_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

//        }
        // Use a custom walker for the ACF dropdowns
        // Change taxonomy id to slug
        add_filter('acf/fields/taxonomy/wp_list_categories', array($this, 'acf_wp_list_categories'), 10, 2);

        if(is_admin()){
            // Import my info when import data from templaza framework
            require_once ADVANCED_PRODUCT_CLASSES_PATH.'/class-import_sync_templaza_framework.php';
            if(class_exists('Advanced_Product\Import_Sync_Templaza_Framework')) {
                $import_sync = new Import_Sync_Templaza_Framework();
            }
        }

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

    /**
     * Use a custom walker for the ACF dropdowns
     * @param  array $args 		The default dropdown arguments
     * @param  array $field 	The Car Dealer field
     * @return array 			Default dropdown args + custom walker
     */
    public function acf_wp_list_categories( $args, $field ) {
        $ap_taxonomies  = array('ap_branch', 'ap_category', 'ap_group_field');

        if(($ap_custom_taxonomies = AP_Custom_Taxonomy_Helper::get_taxonomies()) && is_array($ap_custom_taxonomies)) {
            $ap_taxonomies = array_merge($ap_taxonomies,$ap_custom_taxonomies);
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
    public function load_plugin_textdomain() {
        load_plugin_textdomain( $this -> text_domain, false, ADVANCED_PRODUCT_PATH . '/languages/' );
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
        $post_type = get_post_type();

        if($post_type != 'ap_product'){
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
            wp_enqueue_script('advanced-product');
            wp_add_inline_script('advanced-product', 'var advanced_product = {};', '');
            wp_enqueue_script('advanced-product_admin_scripts');
        }
    }
}

Advanced_Product::instance();