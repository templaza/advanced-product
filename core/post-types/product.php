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

//            add_filter( 'init', array( $this, 'options_custom_fields' ));
//            add_action( 'init', array( $this, 'register_fields' ) );

//            add_action( 'advanced-product/after_init', array( $this, 'options_custom_fields' ) );
            add_action( 'advanced-product/after_init', array( $this, 'register_fields' ) );

//            add_action( 'admin_menu', array( $this, 'remove_taxonomy_metaboxes' ) );
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
//            register_post_type( 'vehicle', $args );
        }

        /**
         * Registers the specification fields that come preinstalled with Car Dealer
         * Use the 'advanced-product/ap_product/built_in_fields' filter to remove fields from it
         */
        public function register_built_in_fields() {

            $built_in_fields = apply_filters( 'advanced-product/'
                .$this ->get_post_type().'/built_in_fields', $this->built_in);


            if ( ! empty( $built_in_fields )) {
                foreach ( $built_in_fields as $field ) {
                    $this->register_field( $field );
                }
            }

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
//            Custom_FieldHelper::get_fields_grouped_taxonomy();
            $acf_fields = AP_Custom_Field_Helper::get_custom_fields();


//            $this->register_built_in_fields();

            $fields = array();

            if($acf_fields){
                foreach ($acf_fields as $acf_field){
                    if($acf_f = AP_Custom_Field_Helper::get_custom_field_option_by_id($acf_field -> ID)) {
                        $fields[] = $acf_f;
                    }
                }
            }

            if(function_exists("register_field_group"))
            {
                register_field_group(array (
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
//                        'layout' => 'no_box',
//                        'hide_on_screen' => array (
//                            /*'the_content',*/ 'custom_fields'
//                        ),
                        'hide_on_screen' => array(),
                    ),
                    'menu_order' => 0,
                ));

            }
        }

        /**
         * returns the sorted registered fields
         * @return [type] [description]
         */
        public function get_registered_fields( $group = '' ) {

            $fields = $this->fields;
            $filtered = array();
            $sorted = array();

            foreach ($fields as $key => $field ) {
                $fields[$key]['label'] = __( $field['label'], $this -> text_domain );
            }

            if ( ! empty( $group ) ) {
                foreach ($fields as $field ) {
                    if ( $group == $field['group'] ) {
                        $filtered[] = $field;
                        // Register custom category
                        if($field['name'] == 'category'){
//                            $filtered[] =
                        }
                    }
                }
            } else {
                $filtered = $fields;
            }

            foreach ( $filtered as $key => $value ) {
                $sorted[$key]  = $value['sort'];
            }

            array_multisort( $sorted, SORT_ASC, SORT_NUMERIC, $filtered );

            return apply_filters( 'advanced-product/'.$this -> get_post_type().'/fields', $filtered );
        }

        public function get_settings_page() {
            return 'edit.php?post_type='.$this -> get_post_type().'&page=acf-options-settings';
        }

        public function options_custom_fields() {

            $custom_fields = get_field( 'ap_custom_fields', 'options' );

            if ( ! empty( $custom_fields )) {
                foreach ( $custom_fields as $i => $field ) {
                    if($field['acf_fc_layout'] =='ap_text') {

                        $args = array(
                            'label' => sanitize_text_field($field['ap_name']),
                            'name' => sanitize_key(sanitize_title($field['ap_name'], 'field_' . mt_rand(100, 100000))),
                            'type' =>'text',
                            'sort' => 100 + $i
                        );
                        if (!empty($field['ap_textvalue'])) {
                            $args['default_value'] = esc_html($field['ap_textvalue']);
                        }
                    }else{
                        $args = array(
                            'label' => sanitize_text_field($field['ap_name']),
                            'name' => sanitize_key(sanitize_title($field['ap_name'], 'field_' . mt_rand(100, 100000))),
                            'instructions' => '',
                            'sort' => 100 + (is_numeric($i)?$i:0)
                        );


                        if (!empty($field['ap_choices'])) {
                            $choices = array_filter(explode(',', $field['ap_choices']));

                            if ('ap_number_field' == $field['acf_fc_layout']) {
                                $meta_values = array_unique($this->get_meta_values($args['name'], $this -> get_post_type()));
                                $choices = array_unique(array_merge($choices, $meta_values));
                            }

                            if (!empty($choices)) {
                                foreach ($choices as $key => $choice) {
                                    unset($choices[$key]);
                                    $args['choices'][sanitize_title($choice)] = $choice;
                                }
                            }
                        }
                        if (!empty($field['acf_fc_layout'])) {
                            $type   = preg_replace('/^ap_/','', $field['acf_fc_layout']);
                            $args['type'] = $field['acf_fc_layout'] == 'ap_option' ? 'radio' : $type;
                        }
                        if (!empty($field['ap_min'])) {
                            $args['min'] = intval($field['ap_min']);
                            $args['default_value'] = intval(@$field['ap_min']);
                        }
                        if (!empty($field['ap_max'])) {
                            $args['max'] = intval($field['ap_max']);
                        }
                        if (!empty($field['ap_append'])) {
                            $args['append'] = esc_html($field['ap_append']);
                        }
                    }
//                    $car_dealer->fields->register_field( $args );
                    $this->register_field( $args );
                }
            }

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

        public function admin_enqueue_scripts(){
//            wp_enqueue_script(array(
//                'acf-input',
//            ));
//            wp_enqueue_script(array(
//                'acf-field-group',
//            ));
//            wp_enqueue_script($this -> get_post_type().'-acf-field-group', AP_Functions::get_my_url().'/includes/library/acf/js/field-group.js');
//            wp_enqueue_script($this -> get_post_type().'-acf-field-group',
//                AP_Functions::get_my_url().'/includes/library/acf/js/input.js',array('advanced-product_admin_scripts'));
        }


    }
}