<?php

namespace Advanced_Product\Post_Type;

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Helper\AP_Product_Helper;
use Advanced_Product\Helper\FieldHelper;
use Advanced_Product\Post_Type;
use Advanced_Product\AP_Functions;

if(!class_exists('Advanced_Product\Post_Type\Custom_Field')){
    class Custom_Field extends Post_Type {

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

            add_action( 'admin_init', array($this, 'disable_autosave') );
            add_action( 'save_post_'.$this -> get_post_type(), array($this, 'save_post'), 10,3 );

            add_filter('pre_trash_post', array($this, 'pre_trash_post'), 10, 2);
            add_filter('pre_delete_post', array($this, 'pre_delete_post'), 10, 3);
            add_filter('post_row_actions', array($this, 'post_row_actions'), 10, 2);
        }

        public function disable_autosave() {
            wp_deregister_script( 'autosave' );
        }

        public function save_post($post_ID, $post, $update){
            global $wpdb;

            // Get acf field attribute
            $acf_fields     = (!empty($_POST) && isset($_POST['fields']))?$_POST['fields']:array();
            $key            = !empty($acf_fields)?array_key_first($acf_fields):'';
            $acf_attribs    = !empty($acf_fields) && $key?$acf_fields[$key]:array();

            if(!empty($acf_attribs)) {
                $my_post = array(
                    'post_name'     => $key,
                    'post_content'  => serialize($acf_attribs),
                    'post_excerpt'  => $acf_attribs['name']
                );
                $wpdb -> update($wpdb -> posts, $my_post, array('ID' => $post_ID));
            }
        }

        public function register(){
            /**
             * Post types
             */
            $singular  = __( 'Custom Field', 'advanced-product' );
            $plural    = __( 'Custom Fields', 'advanced-product' );

            $args = array(
                'description'         => __( 'This is where you can create and manage custom fields.', 'advanced-product' ),
                'labels' => array(
                    'name' 					=> $plural,
                    'singular_name' 		=> $singular,
                    'menu_name'             => $plural,
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
                'supports'            => array( 'title', ),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => 'edit.php?post_type='.$this -> prefix.'product',
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => true,
                'menu_position'       => 20,
//                'menu_icon'           => AP_Functions::get_my_url() . '/assets/images/icon.svg',
                'menu_icon'           => 'dashicons-store',
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => false,
                'query_var'           => false,
                '_builtin' =>  false,
//                'capability_type'     => 'page',
                'capability_type'     => 'post',
                'rewrite' => false,
//                'rewrite'			  => array( 'slug' => 'ap-custom-field' )
            );
            return $args;
        }

        public function manage_edit_columns($columns){
            $new_columns                = array();
            $new_columns['cb']          = $columns['cb'];
            $new_columns['protected']   = __('Protected', 'advanced-product');
//            $new_columns['display_flag']   = __('Display Flag', 'advanced-product');
            $new_columns['in_listing']  = __('In Listing', 'advanced-product');
            $new_columns['in_search']   = __('In Search', 'advanced-product');

            return array_merge($new_columns, $columns);

        }

        public function manage_custom_column($column, $post_id ){
            if($column == 'protected') {
                $protected  = get_post_meta($post_id, '__protected', true);
                if($protected) {
                    echo '<span class="dashicons dashicons-lock"></span>';
                }
            }
            if($column == 'in_listing'){
                // Get post meta
                $in_listing = get_post_meta($post_id, 'show_in_listing', true);
                $in_listing = filter_var($in_listing, FILTER_VALIDATE_BOOLEAN);
                if($in_listing){
                    echo '<span class="dashicons dashicons-yes"></span>';
                }
            }
            if($column == 'in_search'){
                // Get post meta
                $in_search  = get_post_meta($post_id, 'show_in_search', true);
                $in_search  = filter_var($in_search, FILTER_VALIDATE_BOOLEAN);
                if($in_search){
                    echo '<span class="dashicons dashicons-yes"></span>';
                }
            }
        }

        public function admin_enqueue_scripts($hook){
            if($this -> get_post_type() == $this -> get_current_screen_post_type()) {
                wp_enqueue_script('advanced-product_admin_sanitize-title-script');
            }
        }

        /**
         * Deny trash post is protected
         * @param bool|null $trash Whether to go forward with trashing.
         * @param WP_Post   $post  Post object.
         * @return bool|null
         * */
        public function pre_trash_post($trash, $post){
            if(!empty($post) && $post -> post_type == $this -> get_post_type()){
                $is_protected    = (bool) get_post_meta($post -> ID, '__protected', true);
                if($is_protected){
                    $trash  = false;
                }
            }

            return $trash;
        }

        /**
         * Deny delete post is protected
         * @param bool|null $trash Whether to go forward with trashing.
         * @param WP_Post   $post  Post object.
         * @return bool|null
         * */
        public function pre_delete_post($delete, $post){
            if(!empty($post) && $post -> post_type == $this -> get_post_type()){
                $is_protected    = (bool) get_post_meta($post -> ID, '__protected', true);
                if($is_protected){
                    $delete  = false;
                }
            }

            return $delete;
        }

        /**
         * Deny trash action with field is protected
         * @param string[] $actions An array of row action links. Defaults are
         *                          'Edit', 'Quick Edit', 'Restore', 'Trash',
         *                          'Delete Permanently', 'Preview', and 'View'.
         * @param WP_Post  $post    The post object.
         * @return bool|null
         * */
        public function post_row_actions($actions, $post){
            if (!empty($post) && $post->post_type == $this -> get_post_type() && isset($actions['trash']) ) {
                $is_protected    = (bool) get_post_meta($post -> ID, '__protected', true);
                if($is_protected){
                    unset($actions['trash']);
                }
            }

            return $actions;
        }

    }
}