<?php

namespace Advanced_Product\Post_Type;

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Post_Type;
use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\FieldHelper;
use Advanced_Product\Helper\AP_Product_Helper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

if(!class_exists('Advanced_Product\Post_Type\Product')){
    class Product extends Post_Type {

        protected $fields;
        protected $built_in;

        public function __construct($core = null, $post_type = null)
        {
            parent::__construct($core, $post_type);

            $this -> fields     = array();
            $this -> built_in   = FieldHelper::get_core_fields();
        }

        public function hooks()
        {
            parent::hooks();

            add_action( 'advanced-product/after_init', array( $this, 'register_fields' ) );
        }

        public function register(){
            /**
             * Post types
             */
            $singular  = __( 'Product', $this -> text_domain );
            $plural    = __( 'Products', $this -> text_domain );

            $args = array(
                'description'         => __( 'This is where you can create and manage products.', $this -> text_domain ),
                'labels' => array(
//                    'name' 					=> $plural,
                    'name' 					=> __( 'Advanced Products', $this -> text_domain ),
                    'singular_name' 		=> $singular,
                    'menu_name'             => __( 'Advanced Products', $this -> text_domain ),
                    'all_items'             => sprintf( __( 'All %s', $this -> text_domain ), $plural ),
                    'add_new' 				=> __( 'Add New', $this -> text_domain ),
                    'add_new_item' 			=> sprintf( __( 'Add %s', $this -> text_domain ), $singular ),
                    'edit' 					=> __( 'Edit', $this -> text_domain ),
                    'edit_item' 			=> sprintf( __( 'Edit %s', $this -> text_domain ), $singular ),
                    'new_item' 				=> sprintf( __( 'New %s', $this -> text_domain ), $singular ),
                    'view' 					=> sprintf( __( 'View %s', $this -> text_domain ), $singular ),
                    'view_item' 			=> sprintf( __( 'View %s', $this -> text_domain ), $singular ),
                    'search_items' 			=> sprintf( __( 'Search %s', $this -> text_domain ), $plural ),
                    'not_found' 			=> sprintf( __( 'No %s found', $this -> text_domain ), $plural ),
                    'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', $this -> text_domain ), $plural ),
                    'parent' 				=> sprintf( __( 'Parent %s', $this -> text_domain ), $singular )
                ),
                'supports'            => array( 'title', 'editor', 'thumbnail','excerpt', 'custom-fields','comments' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 20,
//                'menu_icon'           => AP_Functions::get_my_url() . '/assets/images/icon.svg',
                'menu_icon'           => 'dashicons-store',
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'rewrite'			  => array( 'slug' => 'ap-product' )
            );
            return $args;
        }

        /**
         * use this function to add additional fields to a car object
         * @param  array $args
         */
        public function register_field( $args ) {

            // ACF requires a unique key per field so lets generate one
            $key = md5( serialize( $args ));

            if ( empty( $args['type'] )) {
                $args['type'] = 'number';
            }
            $type = $args['type'];

            if ( 'taxonomy' == $type ) {
                $field = wp_parse_args( $args, array(
                    'key' => $key,
                    'label' => '',
                    'name' => '',
                    'type' => 'taxonomy',
                    'instructions' => '',
                    'taxonomy' => '',
                    'field_type' => 'select',
                    'allow_null' => 1,
                    'load_save_terms' => 1,
                    'return_format' => 'id',
                    'multiple' => 0,
                    'sort' => 0,
                    'group' => 'overview'
                ) );
            } else if ( 'radio' == $type ) {
                $field = wp_parse_args( $args, array (
                    'key' => $key,
                    'label' => '',
                    'name' => '',
                    'instructions' => '',
                    'choices' => array(),
                    'other_choice' => 1,
                    'save_other_choice' => 1,
                    'default_value' => '',
                    'layout' => 'horizontal',
                    'sort' => 0,
                    'group' => 'specs'
                ) );
            } else if ( 'checkbox' == $type ) {
                $field = wp_parse_args( $args, array (
                    'key' => $key,
                    'label' => '',
                    'name' => '',
                    'instructions' => '',
                    'choices' => array(),
                    'layout' => 'vertical',
                    'sort' => 0,
                    'group' => 'specs'
                ) );
            } else {
                $field = wp_parse_args( $args, array (
                    'key' => $key,
                    'label' => '',
                    'name' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'min' => 0,
                    'max' => '',
                    'step' => '',
                    'sort' => 0,
                    'group' => 'specs'
                ) );
            }
            $field = apply_filters( 'advanced-product/'.$this -> get_post_type().'/register_field', $field );
            $this->fields[$field['name']] = $field;
            AP_Product_Helper::setField($field);

            return $field;
        }

        public function register_fields() {
            if(!function_exists("register_field_group"))
            {
                return;
            }
            $acf_fields = AP_Custom_Field_Helper::get_custom_fields();

            $fields = array();

            if($acf_fields){
                $prev_term_slug = '';
                $gid            = '6216fc1bd1117';
                $goptions       = array (
                    'id' => 'acf_product_property',
                    'title' => __( 'Properties', $this -> text_domain ),
                    'fields' => $fields,
                    'location' => array (
                        array (
                            array (
                                'param' => 'post_type',
                                'operator' => '==',
                                'value' => $this -> get_post_type(),
                                'order_no' => 0,
                                'group_no' => 0,
                            ),
                        ),
                    ),
                    'options' => array (
                        'position' => 'normal',
                        'style' => 'default',
                        'layout' => 'default',
//                        'hide_on_screen' => array (
//                            /*'the_content',*/ 'custom_fields'
//                        ),
                        'hide_on_screen' => array(),
                    ),
                    'menu_order' => 0,
                );

                foreach ($acf_fields as $i => $acf_field){
                    if($acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($acf_field -> ID)) {
                        $next_index = $i + 1;
                        if((isset($acf_fields[$next_index]) && /*isset($acf_field -> term_slug)
                                &&*/ $acf_fields[$next_index] -> term_slug != $acf_field -> term_slug)
                            || ($i == count($acf_fields) -1)){
                            $goptions['id']     = 'acf_product_'.(!empty($acf_field -> term_slug))?$acf_field -> term_slug:$gid;
                            $goptions['title']  = (!empty($acf_field -> term_name))?$acf_field -> term_name:__( 'Properties', $this -> text_domain );
                            $goptions['fields'] = $fields;

                            $goptions['menu_order']  = $i;

                            register_field_group($goptions);
                            $fields = array();
                        }
                        $fields[] = $acf_f;
                    }
                }
            }

//            if(function_exists("register_field_group"))
//            {
//                register_field_group(array (
//                    'id' => 'acf_product_property',
//                    'title' => __( 'Properties', $this -> text_domain ),
//                    'fields' => $fields,
//                    'location' => array (
//                        array (
//                            array (
//                                'param' => 'post_type',
//                                'operator' => '==',
//                                'value' => $this -> get_post_type(),
//                                'order_no' => 0,
//                                'group_no' => 0,
//                            ),
//                        ),
//                    ),
//                    'options' => array (
//                        'position' => 'normal',
//                        'style' => 'default',
//                        'layout' => 'default',
////                        'hide_on_screen' => array (
////                            /*'the_content',*/ 'custom_fields'
////                        ),
//                        'hide_on_screen' => array(),
//                    ),
//                    'menu_order' => 0,
//                ));
//
//            }
        }

        /**
         * Returns all values of given meta key
         * @param  string $key    [description]
         * @param  string $type   [description]
         * @param  string $status [description]
         * @return [type]         [description]
         */
        public function get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

            global $wpdb;

            if( empty( $key ) )
                return;

            $r = $wpdb->get_col( $wpdb->prepare( "
	        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
	        WHERE pm.meta_key = '%s'
	        AND p.post_status = '%s'
	        AND p.post_type = '%s'
	    ", $key, $status, $type ) );

            return $r;
        }

//        public function admin_enqueue_scripts(){
////            wp_enqueue_script(array(
////                'acf-input',
////            ));
////            wp_enqueue_script(array(
////                'acf-field-group',
////            ));
////            wp_enqueue_script($this -> get_post_type().'-acf-field-group', AP_Functions::get_my_url().'/includes/library/acf/js/field-group.js');
////            wp_enqueue_script($this -> get_post_type().'-acf-field-group',
////                AP_Functions::get_my_url().'/includes/library/acf/js/input.js',array('advanced-product_admin_scripts'));
//        }


    }
}