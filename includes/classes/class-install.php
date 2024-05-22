<?php

namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Helper\AP_Helper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

class Install extends Base{

    public function init(){
        $this -> import_custom_fields();
//        $this -> import_products();
//        $this -> create_pages();
    }

    public function install(){
        $result = false;

        $result = $this -> import_custom_fields();
        $result = $this -> import_products();
        $this -> create_pages();

        return $result;
    }

    public function import_custom_fields(){

        $imported_key   = '_advanced_product_custom_field_protected_imported';

        // Check imported
        $imported   = get_option($imported_key, 0) || !empty(get_posts(array(
                'post_type' => 'ap_custom_field'
            )));

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

    public function import_products(){

        $imported_key   = '_advanced_product__products_imported';

        // Check imported
        $imported   = get_option($imported_key, 0) || !empty(get_posts(array(
                'post_type' => 'ap_product'
            )));

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
        $_file  = ADVANCED_PRODUCT_PATH.'/data/products.xml';

        // Replace demo url to client url
        add_filter('wp_import_post_data_raw', array($this, 'override_media_path_import'), 10);

        $importer   = new \Advanced_Product_Importer(array(
            'fetch_remote_file' => false
        ));

        ob_start();
        $importer->import($_file);
        $result = ob_get_contents();
        ob_end_clean();

        remove_filter('wp_import_post_data_raw', array($this, 'override_media_path_import'));

        if($result){
            update_option($imported_key, 1);
        }

        return true;
    }

    public function override_media_path_import($post) {
        if($post['post_type'] == 'attachment'){
            $newAttachUrl   = isset($post['attachment_url'])?($post['attachment_url']):'';
            $newFileName    = basename($newAttachUrl);
            $newFilePath    = wp_unslash(get_home_url().'/wp-content/plugins/advanced-product/data/images-sample/'
                .$newFileName);
            $post['attachment_url'] = $newFilePath;
        }
        return $post;
    }

    public function create_pages(){
        $pages = apply_filters(
            'advanced-product/install/create_pages',
            array(
                'inventory'           => array(
                    'name'    => _x( 'inventory', 'Page slug', 'advanced-product' ),
                    'title'   => _x( 'Inventory', 'Page title', 'advanced-product' ),
                    'content' => '',
                ),
            )
        );

        foreach ( $pages as $key => $page ) {
            $this -> create_page(
                esc_sql( $page['name'] ),
                'ap_' . $key . '_page_id',
                $page['title'],
                $page['content'],
                ! empty( $page['parent'] ) ? AP_Helper::get_page_id( $page['parent'] ) : '',
                ! empty( $page['post_status'] ) ? $page['post_status'] : 'publish'
            );
        }
    }

    protected function create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0, $post_status = 'publish' ) {
        global $wpdb;

        $acf_option_name    = 'options_'.$option;
//        $option_value = get_option( $option );
        $option_value = get_option( $acf_option_name );

        if ( $option_value > 0 ) {
            $page_object = get_post( $option_value );

            if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ), true ) ) {
                // Valid page is already in place.
                return $page_object->ID;
            }
        }

//        $page_object = get_field($option, 'option');
//        if(!empty($page_object)){
//            if(!($page_object instanceof \WP_Post) && is_numeric($page_object)){
//                $page_object = get_post( $page_object );
//            }
//
//            if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ), true ) ) {
//                // Valid page is already in place.
//                return $page_object->ID;
//            }
//        }

        if ( strlen( $page_content ) > 0 ) {
            // Search for an existing page with the specified page content (typically a shortcode).
            $shortcode        = str_replace( array( '<!-- wp:shortcode -->', '<!-- /wp:shortcode -->' ), '', $page_content );
            $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$shortcode}%" ) );
        } else {
            // Search for an existing page with the specified page slug.
            $valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
        }

        /* phpcs:disable WooCommerce.Commenting.CommentHooks.MissingHookComment */
        $valid_page_found = apply_filters( 'advanced-product_create_page_id', $valid_page_found, $slug, $page_content );
        /* phpcs: enable */

        if ( $valid_page_found ) {
            if ( $option ) {
                update_option('_'.$acf_option_name, 'field_'.$option);
                update_option( $acf_option_name, $valid_page_found );
            }
            return $valid_page_found;
        }

        // Search for a matching valid trashed page.
        if ( strlen( $page_content ) > 0 ) {
            // Search for an existing page with the specified page content (typically a shortcode).
            $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
        } else {
            // Search for an existing page with the specified page slug.
            $trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
        }

        if ( $trashed_page_found ) {
            $page_id   = $trashed_page_found;
            $page_data = array(
                'ID'          => $page_id,
                'post_status' => $post_status,
            );
            wp_update_post( $page_data );
        } else {
            $page_data = array(
                'post_status'    => $post_status,
                'post_type'      => 'page',
                'post_author'    => 1,
                'post_name'      => $slug,
                'post_title'     => $page_title,
                'post_content'   => $page_content,
                'post_parent'    => $post_parent,
                'comment_status' => 'closed',
            );
            $page_id   = wp_insert_post( $page_data );

            do_action( 'advanced-product_page_created', $page_id, $page_data );
        }

        if ( $option ) {
            update_option('_'.$acf_option_name, 'field_'.$option);
            update_option( $acf_option_name, $page_id );
        }

        return $page_id;
    }
}