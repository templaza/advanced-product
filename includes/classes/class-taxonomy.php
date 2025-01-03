<?php
namespace Advanced_Product;

use Advanced_Product\Helper\AP_Custom_Taxonomy_Helper;
use Advanced_Product\Helper\FieldHelper;
use Advanced_Product\Helper\TaxonomyHelper;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\Taxonomy')) {
    class Taxonomy extends Base
    {
        protected $prefix               = 'ap_';
        protected $allow_custom_options = true;
        protected $field_registered     = array();
        protected $acf_input_controller = array();

        protected $old_slug_before_save = '';

        public function __construct($core = null, $post_type = null)
        {
            global $pagenow;
            register_activation_hook( ADVANCED_PRODUCT . '/' . ADVANCED_PRODUCT.'.php', array( $this, 'register_taxonomy' ) );

            parent::__construct($core, $post_type);

        }

        public function hooks(){
            parent::hooks();

            // Register taxonomy hook
            add_action('init', array($this, 'register_taxonomy'),11);

            add_action('advanced-product/after_init', array($this, 'register_acf'),11);
            add_action('saved_term', array($this, 'saved_term'), 11, 4);

            // Filter to get old slug before update term
            add_filter('wp_update_term_data', array($this, 'wp_update_term_data'), 10, 3);

            // Manage taxonomy header column list hook
            add_filter('manage_edit-'.$this ->get_taxonomy_name().'_columns', array($this, 'manage_edit_columns'), 12);
            // Manage taxonomy content column list hook
            add_filter( 'manage_' . $this ->get_taxonomy_name() . '_custom_column', array($this, 'manage_custom_column'), 12, 3 );

        }

        public function register_acf(){
            // Register acf input controller to add fields with edit term taxonomy
            $acf    = advanced_product_acf();
            if(/*$pagenow == 'term.php' &&*/ $acf && version_compare($acf -> settings['version'], '5.0', '<') &&
                $this ->get_current_screen_taxonomy() == $this ->get_taxonomy_name()) {
                new ACF_Taxonomy($this -> core, $this -> post_type, array(
                    'taxonomy_name' => $this -> get_taxonomy_name()
                ));
            }
        }

        // Get taxonomy name by class name
        public function get_taxonomy_name(){
            $store_id   = __METHOD__;
            $store_id   = md5($store_id);

            if(isset($this -> cache[$store_id])){
                return $this -> cache[$store_id];
            }

            $class_name = get_class($this);
            $class_name = preg_replace('#^'.addslashes(__CLASS__).'\\\\#i', '', $class_name);
            $class_name = preg_replace('/^Advanced_Product\\\\Taxonomy\\\\/i', '', $class_name);
            $class_name = $this -> prefix.strtolower($class_name);


            $this -> cache[$store_id]   = $class_name;

            return $class_name;
        }

        public function my_taxonomy_exists(){
            $taxonomy  = $this -> get_taxonomy_name();
            if(taxonomy_exists($taxonomy) && $this -> get_current_screen_taxonomy() == $taxonomy) {
                return true;
            }
            return false;
        }

        public function get_current_screen_taxonomy() {

            global $post, $typenow, $current_screen;

            if($current_screen && $current_screen->taxonomy) return $current_screen->taxonomy;

            elseif(isset($_REQUEST['taxonomy'])) return sanitize_key($_REQUEST['taxonomy']);

            return null;

        }

        public function register_taxonomy(){
            $taxonomy  = $this->get_taxonomy_name();

            if(!taxonomy_exists($taxonomy)){
                // Register post type to wordpress
                if(method_exists($this, 'register')) {
                    $args   = $this -> register();
                    $tax_obj    = \register_taxonomy($taxonomy, $args['object_type'], $args['args']);

                    if($tax_obj){
                        if(method_exists($this, 'registered')) {
                            call_user_func(array($this, 'registered'), array($tax_obj));
                        }

                        do_action('advanced-product/taxonomy/'.$taxonomy.'/registered', $taxonomy, $tax_obj, $this);
                        do_action('advanced-product/taxonomy/registered', $taxonomy, $tax_obj, $this);

                        flush_rewrite_rules();
                    }
                }
            }
        }

        /*
         *  Registered taxonomy
         *
         */
        public function registered($tax){
            if(is_admin()) {
                if($this -> allow_custom_options) {
                    $this->register_core_fields();
                }

                if(method_exists($this, 'register_fields')){
                    call_user_func(array($this, 'register_fields'));
                }
            }
        }

        public function wp_update_term_data($data, $taxonomy, $args){
            $old_term = get_term($taxonomy, $this -> get_taxonomy_name());
            if(!empty($old_term) && !is_wp_error($old_term)) {
                $this -> old_slug_before_save = $old_term->slug;
            }
            return $data;
        }

        public function saved_term($term_id, $tt_id, $taxonomy, $update){

            $fields = isset($_POST['fields'])?$_POST['fields']:array();

            // loop through and save
            if( $fields && !empty($fields) )
            {
                // loop through and save $_POST data
                foreach( $_POST['fields'] as $k => $v )
                {
                    // get field
                    $f = apply_filters('acf/load_field', false, $k );

                    $acf    = new \acf_field_functions();

                    // update field
                    do_action('acf/update_value', $v, 'term_'.$term_id, $f, $taxonomy );

                }
            }

            $taxonomies = array(
                $this -> get_taxonomy_name()
            );

            // Update taxonomy-slug associated to category when taxonomy changed
            $custom_categories  = AP_Custom_Taxonomy_Helper::get_taxonomies();

            if(!empty($custom_categories) && !is_wp_error($custom_categories)){
                foreach ($custom_categories as $custom_cat){
                    $f_cat_slug = \get_field('slug', $custom_cat -> ID);

                    if(empty($f_cat_slug)){
                        continue;
                    }

                    $taxonomies[]   = $f_cat_slug;
                }
            }

            // Check this taxonomy is our terms
            $term       = get_term( $term_id );
            if(empty($term) || is_wp_error($term) || !in_array($taxonomy, $taxonomies)){
                return;
            }

            if(!empty($this -> old_slug_before_save) && $this -> old_slug_before_save != $term -> slug) {
                global $wpdb;

                // Update branch-slug with product data
                $q  = 'UPDATE '.$wpdb -> postmeta.' AS pm';
                $q .= ' INNER JOIN '.$wpdb ->posts.' AS p ON p.id = pm.post_id';
                $q .= ' SET pm.meta_value=REPLACE(pm.meta_value, "'.$this -> old_slug_before_save.'", "'.$term -> slug.'")';
                $q .= ' WHERE pm.meta_key IN (
                SELECT post_excerpt FROM '.$wpdb -> posts.'
                WHERE post_type="ap_custom_field"
                AND post_content LIKE "%'.addslashes('s:4:"type";s:8:"taxonomy"').'%" AND post_content LIKE "%'
                    .addslashes('s:8:"taxonomy";s:9:"'.$this -> get_taxonomy_name().'"').'%"
                )';
                $q  .= ' AND(pm.meta_value = "'.$this -> old_slug_before_save.'" OR pm.meta_value LIKE "%\"'
                    .$this -> old_slug_before_save.'\"%")';
                $wpdb -> query($q);
                wp_reset_query();

                if(!empty($taxonomies)){
                    // Update taxonomy slug associated to
                    foreach ($taxonomies as $f_cat_slug){
                        $q  = 'UPDATE '.$wpdb -> termmeta.' AS tm';
                        $q .= ' INNER JOIN '.$wpdb ->term_taxonomy.' AS tt ON tt.term_id = tm.term_id AND tt.taxonomy="'.$f_cat_slug.'"';
                        $q .= ' INNER JOIN '.$wpdb ->terms.' AS t ON t.term_id = tm.term_id';
                        $q .= ' SET tm.meta_value=REPLACE(tm.meta_value, "'.$this -> old_slug_before_save.'", "'.$term->slug.'")';
                        $q .= ' WHERE tm.meta_key = "'.$this -> get_taxonomy_name().'"';
                        $q .= ' AND tm.meta_value LIKE \'%"'.$this -> old_slug_before_save.'"%\'';
                        $wpdb -> query($wpdb -> prepare($q));
                        wp_reset_query();
                    }
                }
            }
        }

        public function __get_core_field_group_id(){
            return 'acf_'.$this -> get_taxonomy_name().'-properties';
        }

        public function __get_core_fields(){
            $fields = array(
                array(
                    'key'           => 'field_'.md5($this -> get_taxonomy_name()),
                    'label'         => __('Image', 'advanced-product'),
                    'name'          => 'image',
                    'type'          => 'image',
                    'wp_type'       => 'taxonomy',
                    'default_value' => '',
                    'group' => $this -> __get_core_field_group_id()
                )
            );

            return apply_filters('advanced-product/'.$this -> get_taxonomy_name().'/fields/create', $fields);
        }
        public function __get_core_field_group(){
            $store_id   = __METHOD__;
            $store_id  .= ':'.$this -> get_taxonomy_name();
            $store_id   = md5($store_id);

            if(isset($this -> cache[$store_id])){
                return $this -> cache[$store_id];
            }

            $fields_group = array(
                'id' => $this -> __get_core_field_group_id(),
                'title' => __('Property', 'advanced-product'),
                'fields' => $this -> __get_core_fields(),
                'location' => array(
                    array(
                        array(
                            'param'     => 'ef_taxonomy', /* acf v5 is taxonomy*/
                            'operator'  => '==',
                            'value'     => $this -> get_taxonomy_name(),
                            'order_no' => 0,
                            'group_no' => md5($this -> get_taxonomy_name()),
                        ),
                    ),
                ),
                'options' => array(
                    'position' => 'normal',
                    'layout' => 'no_box',
                    'hide_on_screen' => array(),
                ),
                'menu_order' => 0,
            );

            $fields = apply_filters('advanced-product/'.$this -> get_taxonomy_name().'/fields/registered', $fields_group['fields']);

            $fields_group['fields'] = $fields;

            $fields_group = apply_filters('advanced-product/'.$this -> get_taxonomy_name().'/fields_group/registered', $fields_group);

            if(count($fields_group)) {
                $this -> cache[$store_id]   = $fields_group;
                return $fields_group;
            }
            return array();
        }

        public function register_core_fields(){
            if(!function_exists('register_field_group'))
            {
                return false;
            }
            $store_id   = __METHOD__;
            $store_id  .= ':'.$this -> get_taxonomy_name();
            $store_id   = md5($store_id);

            if(isset($this -> cache[$store_id])){
                return $this -> cache[$store_id];
            }

            \register_field_group($this -> __get_core_field_group());

            $this -> cache[$store_id]   = true;

        }

        public function manage_edit_columns($columns){

            $fields = FieldHelper::get_fields_by_group($this -> __get_core_field_group_id(),
                array('ef_taxonomy' => $this -> get_taxonomy_name()));

            if(!count($fields)){
                return $columns;
            }

            $new_columns            = array();

            if(isset($columns['cb'])) {
                $new_columns['cb'] = $columns['cb'];
            }

            $new_columns['thumb']   = '<span class="dashicons dashicons-format-image"></span>';

            return array_merge($new_columns, $columns);
        }

        /**
         * Field column value added to category admin.
         *
         * @access public
         * @param mixed $content
         * @param mixed $column
         * @param mixed $id
         * @return void
         */
        public function manage_custom_column($content, $column, $term_id ){

            $fields = FieldHelper::get_fields_by_group($this -> __get_core_field_group_id(),
                array('ef_taxonomy' => $this -> get_taxonomy_name()));

            if(!count($fields)){
                return $content;
            }

            if($column == 'thumb'){
                $imgField = get_field( 'image', 'term_'.$term_id );
                $url    = (!empty($imgField) && isset($imgField['sizes']) && isset($imgField['sizes']['thumbnail']))?$imgField['sizes']['thumbnail']:'';

                if(!isset($imgField['sizes']) && !isset($imgField['sizes']['thumbnail'])){
                    if($img = \wp_get_attachment_image_src($imgField)) {
                        $url = $img[0];
                    }
                }

                if(!empty($url)) {
                    $content = '<img src="' . $url . '" alt="" class="wp-post-image" style="max-width: 40px; max-height: 40px;"/>';
                }
            }

            return $content;
        }
    }
}