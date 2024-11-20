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
            add_action( 'wp_after_insert_post', array( $this, 'create_taxonomy' ), 10, 2 );
            add_filter('pre_trash_post', array($this, 'pre_trash_post'), 10, 2);
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

        /**
         * Deny trash post is protected
         * @param bool|null $trash Whether to go forward with trashing.
         * @param WP_Post   $post  Post object.
         * @return bool|null
         * */
        public function pre_trash_post($trash, $post){
            if(!empty($post) && $post -> post_type == $this -> get_post_type()){

                $custom_field_id = get_post_meta($post -> ID, 'ap_custom_category_taxonomy', true);
                if($custom_field_id){
                    wp_trash_post($custom_field_id);
                }
            }

            return $trash;
        }

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
        public function create_taxonomy($post_ID, $post){

            if ( 'ap_custom_category' === get_post_type( $post ) ) {
                $tax_slug = get_post_meta($post_ID, 'slug', true);
                $custom_category_tax = get_post_meta($post_ID,'ap_custom_category_taxonomy',true);
                if(empty($custom_category_tax) && $post->post_status != 'auto-draft' && $post->post_status != 'trash'){
                    $new_post_key = uniqid('field_');
                    $ap_custom_post = array (
                        'label' => ''.$post -> post_title.'',
                        'name' => 'ap_'.$tax_slug.'',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => '0',
                        'icon' =>
                            array (
                                'icon' => '',
                                'type' => '',
                            ),
                        'icon_image' => '',
                        'wrapper_attribute' =>
                            array (
                                'width' => '',
                            ),
                        'taxonomy' => ''.$tax_slug.'',
                        'field_type' => 'checkbox',
                        'allow_null' => '0',
                        'load_save_terms' => '1',
                        'return_format' => 'id',
                        's_type' => 'select',
                        'conditional_logic' =>
                            array (
                                'status' => '0',
                                'rules' =>
                                    array (
                                        0 =>
                                            array (
                                                'field' => 'field_63e370f99bdcb',
                                                'operator' => '==',
                                                'value' => 'sale',
                                            ),
                                    ),
                                'allorany' => 'all',
                            ),
                        'order_no' => 0,
                        'key' => ''.$new_post_key.'',
                    );
                    $new_post_content = serialize($ap_custom_post);
                    $new_post = array(
                        'post_title'    => $post -> post_title,
                        'post_name'    => $new_post_key,
                        'post_excerpt'  => $tax_slug,
                        'post_type'    => 'ap_custom_field',
                        'post_content'  => $new_post_content,
                        'post_status'   => 'publish',
                        'post_author'   => 1,
                    );

                    $new_post_id = wp_insert_post( $new_post );
                    if ( ! add_post_meta( $new_post_id, ''.$new_post_key.'', ''.$new_post_content.'', true ) ) {
                        update_post_meta ( $new_post_id, ''.$new_post_key.'', ''.$new_post_content.'' );
                    }
                    if ( ! add_post_meta( $new_post_id, 'ap_taxonomy_custom_category', ''.$post_ID.'', true ) ) {
                        update_post_meta ( $new_post_id, 'ap_taxonomy_custom_category', ''.$post_ID.'' );
                    }
                    if ( ! add_post_meta( $post_ID, 'ap_custom_category_taxonomy', ''.$new_post_id.'', true ) ) {
                        update_post_meta ( $post_ID, 'ap_custom_category_taxonomy', ''.$new_post_id.'' );
                    }
//                    if(get_post_meta($post_ID,'associate_to')){
//                        update_post_meta ( $post_ID, 'associate_to', ''.$tax_slug.'' );
//                    }
                }
                if($custom_category_tax){
                    $post_custom_field_tax_id = $custom_category_tax;
                    $custom_field_tax_category = get_post_meta($post_custom_field_tax_id,'ap_taxonomy_custom_category',true);
                    if($custom_field_tax_category){
                        $post_custom_field_tax = get_post( $post_custom_field_tax_id, ARRAY_A );
                        $custom_field_tax_slug = $post_custom_field_tax['post_name'];
                        $ap_custom_value_update = array (
                            'label' => ''.$post -> post_title.'',
                            'name' => 'ap_'.$tax_slug.'',
                            'type' => 'taxonomy',
                            'instructions' => '',
                            'required' => '0',
                            'icon' =>
                                array (
                                    'icon' => '',
                                    'type' => '',
                                ),
                            'icon_image' => '',
                            'wrapper_attribute' =>
                                array (
                                    'width' => '',
                                ),
                            'taxonomy' => ''.$tax_slug.'',
                            'field_type' => 'checkbox',
                            'allow_null' => '0',
                            'load_save_terms' => '1',
                            'return_format' => 'id',
                            's_type' => 'select',
                            'conditional_logic' =>
                                array (
                                    'status' => '0',
                                    'rules' =>
                                        array (
                                            0 =>
                                                array (
                                                    'field' => 'field_63e370f99bdcb',
                                                    'operator' => '==',
                                                    'value' => 'sale',
                                                ),
                                        ),
                                    'allorany' => 'all',
                                ),
                            'order_no' => 0,
                            'key' => ''.$custom_field_tax_slug.'',
                        );
                        $new_value_update = serialize($ap_custom_value_update);
                        $data = array(
                            'ID' => $post_custom_field_tax_id,
                            'post_content' => $new_value_update,
                            'post_title'    => $post -> post_title,
                            'post_type'    => 'ap_custom_field',
                            'post_excerpt'  => $tax_slug,
                        );
                        wp_update_post( $data);
                        update_post_meta ( $post_custom_field_tax_id, ''.$custom_field_tax_slug.'', ''.$new_value_update.'' );
                    }
                }
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
                if($associate_to){
                    $taxonomy     = get_taxonomy($associate_to);
                    echo '<a href="edit-tags.php?taxonomy='.$associate_to.'&post_type=ap_product">'.$taxonomy -> label.'</a>';
                }

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
                            'allow_null' => true,
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