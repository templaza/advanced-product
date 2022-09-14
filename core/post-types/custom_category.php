<?php

namespace Advanced_Product\Post_Type;

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Post_Type;

if(!class_exists('Advanced_Product\Post_Type\Custom_Category')){
    class Custom_Category extends Post_Type {

        public function __construct($core = null, $post_type = null)
        {
            parent::__construct($core, $post_type);

        }

        public function hooks()
        {
            parent::hooks();
            add_action( 'init', array( $this, 'register_fields' ) );

//            add_action( 'wp_insert_post_data', array( $this, 'insert_post_data' ), 10, 2 );

            add_action( 'save_post_'.$this -> get_post_type(), array($this, 'save_post'), 10,3 );

        }

        public function register(){
            /**
             * Post types
             */
            $singular  = __( 'Custom Category', 'advanced-product' );
            $plural    = __( 'Custom Categories', 'advanced-product' );

            $args = array(
                'description'         => __( 'This is where you can create and manage products.', 'advanced-product' ),
                'labels' => array(
                    'name' 					=> $plural,
                    'singular_name' 		=> $singular,
                    'menu_name'             => $plural,
//                    'all_items'             => sprintf( __( 'All %s', 'advanced-product' ), $plural ),
                    'all_items'             => $plural,
                    'add_new' 				=> __( 'Add New', 'advanced-product' ),
                    'add_new_item' 			=> sprintf( __( 'Add %s', 'advanced-product' ), $singular ),
                    'edit' 					=> __( 'Edit', 'advanced-product' ),
                    'edit_item' 			=> sprintf( __( 'Edit %s', 'advanced-product' ), $singular ),
                    'new_item' 				=> sprintf( __( 'New %s', 'advanced-product' ), $singular ),
                    'view' 					=> sprintf( __( 'View %s', 'advanced-product' ), $singular ),
                    'view_item' 			=> sprintf( __( 'View %s', 'advanced-product' ), $singular ),
                    'search_items' 			=> sprintf( __( 'Search %s', 'advanced-product' ), $plural ),
                    'not_found' 			=> sprintf( __( 'No %s found', 'advanced-product' ), $plural ),
                    'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'advanced-product' ), $plural ),
                    'parent' 				=> sprintf( __( 'Parent %s', 'advanced-product' ), $singular )
                ),
                'supports'            => array( 'title'),
//                'supports'            => false,
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => 'edit.php?post_type='.$this -> prefix.'product',
//                'show_in_menu'        => 'edit.php?post_type='.$this -> prefix.'product&page=acf-options-settings',
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 20,
                'menu_icon'           => 'dashicons-store',
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',

                'query_var'                 => false,
//                'rewrite'			  => array( 'slug' => 'subcategory' )
            );
            return $args;
        }

//        public function insert_post_data($post, $postarr){
//            if(isset($post['post_type']) && $post['post_type'] == $this -> get_post_type()){
//                $fields = isset($_POST['fields'])?$_POST['fields']:array();
//
//                if(empty($post['post_title']) && isset($fields['field_618a325158397']) && !empty($fields['field_618a325158397'])){
//                    $post['post_title']  = $fields['field_618a325158397'];
//                }
//            }
//            return $post;
//        }

        public function save_post($post_ID, $post, $update){
            global $wpdb;
            $term_slug  = \get_field('slug', $post_ID);

            $my_post    = array();

            if(!empty($term_slug)){
                $my_post['post_name']  = $term_slug;
            }

            if(!isset($post -> post_title) || empty($post -> post_title)){
                $singular_name  = \get_field('singular_name', $post_ID);
                $my_post['post_title']  = $singular_name;
            }
            if(!empty($my_post)){
                $wpdb -> update($wpdb -> posts, $my_post, array('ID' => $post_ID));
            }
        }

        public function manage_edit_columns($columns){
            $new_columns            = array();
            $new_columns['cb']      = $columns['cb'];
            $new_columns['title']   = $columns['title'];

            $new_columns['associate_to']   = __('Associate To', 'advanced-product');

            return array_merge($new_columns, $columns);
        }

        public function manage_custom_column($column, $post_id ){
            if($column == 'associate_to'){
                $associate_to = get_field( 'associate_to', $post_id );
                $taxonomy     = get_taxonomy($associate_to);

                echo '<a href="edit-tags.php?taxonomy='.$associate_to.'&post_type=ap_product">'.$taxonomy -> label.'</a>';
            }

            return $column;
        }


        /**
         * Removes the default taxonomy metaboxes from the edit screen.
         * We use the advanced custom fields instead and sync the data.
         */
        public function remove_taxonomy_metaboxes(){
//            remove_meta_box( 'tagsdiv-make', 'vehicle', 'normal' );
//            remove_meta_box( 'tagsdiv-model', 'vehicle', 'normal' );
            remove_meta_box( 'tagsdiv-'.$this -> get_post_type(), $this-> get_post_type(), 'normal' );
        }

        public function register_fields() {
            if(function_exists("register_field_group"))
            {
                register_field_group(array (
                    'id' => 'acf_subcategory_general',
                    'title' => __( 'General', 'advanced-product' ),
                    'fields' => array(
//                        array (
//                            'key' => 'field_'.uniqid(),
//                            'label' => __( 'General', 'advanced-product' ),
//                            'name' => '',
//                            'type' => 'tab',
//                        ),
                        array (
                            'key' => 'field_618a30dd427f8',
                            'label' => __( 'Plural name', 'advanced-product' ),
                            'name' => 'plural_name',
                            'type' => 'text',
                            'column_width' => 35,
                            'default_value' => '',
//                            'placeholder' => __( 'E.g. "Horsepower"', 'advanced-product' ),
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'none',
                            'maxlength' => '',
                            'required' => true,
                        ),
                        array (
                            'key' => 'field_618a325158397',
                            'label' => __( 'Singular name', 'advanced-product' ),
                            'name' => 'singular_name',
                            'type' => 'text',
                            'column_width' => 35,
                            'default_value' => '',
//                            'placeholder' => __( 'E.g. "Horsepower"', 'advanced-product' ),
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'none',
                            'maxlength' => '',
                            'required' => true,
                        ),
                        array (
                            'key' => 'field_618a328c52627',
                            'label' => __( 'Slug', 'advanced-product' ),
                            'name' => 'slug',
                            'type' => 'text',
                            'column_width' => 35,
                            'default_value' => '',
//                            'placeholder' => __( 'E.g. "Horsepower"', 'advanced-product' ),
                            'prepend' => '',
                            'append' => '',
                            'formatting' => 'none',
                            'maxlength' => '',
                            'required' => true,
                        ),
                        array (
                            'key' => 'field_618a33aa67454',
                            'label' => __('Associate To', 'advanced-product'),
                            'name' => 'associate_to',
                            'type' => 'select',
//                            'type' => 'taxonomy',
//                            'taxonomy' => 'ap_branch',
                            'field_type' => 'select',
//                            'field_type' => 'multi_select',
                            'allow_null' => false,
                            'load_save_terms' => 0,
                            'choices' => $this -> _categories_associate(),
//                            'return_format' => 'array',
//                            'multiple' => 1,
//                            'instructions' => __('Press and hold the CTRL key and click items in the list to select multiple items. ', 'advanced-product'),
//                            'default_value' => ''
                        ),
//                        array (
//                            'key' => 'field_'.uniqid(),
//                            'label' => __( 'Labels', 'advanced-product' ),
//                            'name' => '',
//                            'type' => 'tab',
//                        ),
//                        array (
//                            'key' => 'field_618a33326b07c',
//                            'label' => __( 'Not found', 'advanced-product' ),
//                            'name' => 'not_found',
//                            'type' => 'text',
//                            'column_width' => 35,
//                            'default_value' => '',
////                            'placeholder' => __( 'E.g. "Horsepower"', 'advanced-product' ),
//                            'prepend' => '',
//                            'append' => '',
//                            'formatting' => 'none',
//                            'maxlength' => '',
//                        ),
                    ),
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
                        'layout' => 'default',
                        'hide_on_screen' => array (
                            'title','the_content', 'custom_fields'
                        ),
//                        'hide_on_screen' => array(),
                    ),
                    'menu_order' => 0,
                ));

            }
        }

        protected function _categories_associate(){
            global $pagenow;
            $categories = array(
                'ap_branch'     => __('Branch', 'advanced-product'),
                'ap_category'   => __('Category', 'advanced-product'),
            );
            $args   = array(
                'order' => 'ASC',
                'orderby'   => 'ID',
                'post_type' => $this -> get_post_type(),
            );
            if($pagenow == 'post.php' && isset($_GET['post']) && !empty($_GET['post'])){
                $args['post__not_in']   =  array($_GET['post']);
            }
            $custom_categories  = get_posts($args);
            if(count($custom_categories)){
                foreach ($custom_categories as $cp){
                    $slug       = get_post_meta( $cp -> ID, 'slug', true);
                    $singular   = get_post_meta( $cp -> ID, 'singular_name', true);
                    $categories[$slug]  = $singular;
                }
            }
            return $categories;
        }

        public function admin_enqueue_scripts(){
            if ( $this -> get_post_type() == \get_post_type() ) {
                wp_enqueue_script('advanced-product_admin_sanitize-title-script', array('advanced-product_admin_scripts'));
                wp_dequeue_script('autosave');
                wp_deregister_script('autosave');
            }
        }

    }
}