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
//
//            add_action('init', array($this, 'register_taxonomy'));


//            add_filter( 'init', array( $this, 'options_custom_fields' ));
            add_action( 'admin_init', array($this, 'disable_autosave') );
            add_action( 'save_post_'.$this -> get_post_type(), array($this, 'save_post'), 10,3 );
//            add_action( 'pre_post_update'.$this -> get_post_type(), array($this, 'save_post'), 10,3 );
//            add_action('admin_menu',array($this, 'admin_menu'));

//            \add_action( 'init', array( $this, 'register_fields' ) );
//            add_action('admin_head', array($this, 'add_meta_boxes'));
//            add_action( 'admin_menu', array( $this, 'remove_taxonomy_metaboxes' ) );
        }

//        public function admin_menu(){
//            add_submenu_page('edit.php?post_type=ap_product', 'Group Fields', 'Group Fields', 'manage_options','edit-tags.php?taxonomy=ap_group_fields&post_type=ap_product'/*,'bsp_students_add'*/);
//        }

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
            $singular  = __( 'Custom Field', $this -> text_domain );
            $plural    = __( 'Custom Fields', $this -> text_domain );

            $args = array(
                'description'         => __( 'This is where you can create and manage custom fields.', $this -> text_domain ),
                'labels' => array(
                    'name' 					=> $plural,
                    'singular_name' 		=> $singular,
                    'menu_name'             => $plural,
                    'all_items'             => $plural,
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
            $new_columns['protected']   = __('Protected', $this -> text_domain);
//            $new_columns['display_flag']   = __('Display Flag', $this -> text_domain);
            $new_columns['in_listing']  = __('In Listing', $this -> text_domain);
            $new_columns['in_search']   = __('In Search', $this -> text_domain);

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
////            wp_enqueue_script(array(
////                'acf-field-group',
////            ));
////            wp_enqueue_script($this -> get_post_type().'-acf-field-group', AP_Functions::get_my_url().'/includes/library/acf/js/field-group.js');
////            wp_enqueue_script($this -> get_post_type().'-acf-field-group',
////                AP_Functions::get_my_url().'/includes/library/acf/js/input.js',array('advanced-product_admin_scripts'));
        }


    }
}