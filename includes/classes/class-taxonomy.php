<?php
namespace Advanced_Product;

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

        public function __construct($core = null, $post_type = null)
        {
            global $pagenow;
            register_activation_hook( ADVANCED_PRODUCT . '/' . ADVANCED_PRODUCT.'.php', array( $this, 'register_taxonomy' ) );

//            // Register acf input controller to add fields with edit term taxonomy
//            $acf    = acf();
//            if(/*$pagenow == 'term.php' &&*/ $acf && version_compare($acf -> settings['version'], '5.0', '<') &&
//                $this ->get_current_screen_taxonomy() == $this ->get_taxonomy_name()) {
//                new ACF_Taxonomy($core, $post_type, array(
//                    'taxonomy_name' => $this -> get_taxonomy_name()
//                ));
//            }

            parent::__construct($core, $post_type);

        }

        public function hooks(){
            parent::hooks();

//            // Register core fields
//            add_action('plugins_loaded', array($this, 'register_core_fields'));
//
//            // Register Fields
//            if(method_exists($this, 'register_fields')) {
//                add_action('plugins_loaded', array($this, 'register_fields'));
//            }

            // Register taxonomy hook
            add_action('init', array($this, 'register_taxonomy'),11);

            add_action('advanced-product/after_init', array($this, 'register_acf'),11);

            // Manage taxonomy header column list hook
            add_filter('manage_edit-'.$this ->get_taxonomy_name().'_columns', array($this, 'manage_edit_columns'), 12);
            // Manage taxonomy content column list hook
            add_filter( 'manage_' . $this ->get_taxonomy_name() . '_custom_column', array($this, 'manage_custom_column'), 12, 3 );
        }

        public function register_acf(){
            // Register acf input controller to add fields with edit term taxonomy
//            $acf    = acf();
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
            if(post_type_exists($taxonomy) && $this -> get_current_screen_taxonomy() == $taxonomy) {
                return true;
            }
            return false;
        }

        public function get_current_screen_taxonomy() {

            global $post, $typenow, $current_screen;

            if ($post && $post->post_type) return $post->post_type;

            elseif($typenow) return $typenow;

            elseif($current_screen && $current_screen->taxonomy) return $current_screen->taxonomy;

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

        public function __get_core_field_group_id(){
            return 'acf_'.$this -> get_taxonomy_name().'-properties';
        }

        public function __get_core_fields(){
            $fields = array(
                array(
                    'key' => 'field_'.md5($this -> get_taxonomy_name()),
                    'label' => __('Image', $this->text_domain),
                    'name' => 'image',
                    'type' => 'image',
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
                'title' => __('Property', $this->text_domain),
                'fields' => $this -> __get_core_fields(),
                'location' => array(
                    array(
                        array(
                            'param'     => 'ef_taxonomy', /* acf v5 is taxonomy*/
                            'operator'  => '==',
                            'value'     => $this -> get_taxonomy_name(),
                            'order_no' => 0,
                            'group_no' => md5($this -> get_taxonomy_name()),
//                            'group_no' => "6188abe73f914",
//                            'group_no' => 0,
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