<?php
namespace Advanced_Product\Shortcode;

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Base;
use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Custom_Field_Helper;
use Advanced_Product\Helper\AP_Product_Helper;

class Search extends Base {

    public function __construct($core = null, $post_type = null) {
        parent::__construct($core, $post_type);

//        register_activation_hook(  ADVANCED_PRODUCT . '/' . ADVANCED_PRODUCT, 'flush_rewrite_rules', 15 );
    }

    public function hooks(){
//        add_shortcode( 'vehicle_searchform', array( $this, 'get_vehicle_searchform' ) );
        add_shortcode( 'advanced-product-form', array( $this, 'get_search_form' ) );

        if(!is_admin()) {
            add_filter('query_vars', array($this, 'add_query_vars_filter'));
            add_action('pre_get_posts', array($this, 'change_event_posts_per_page'), 20);

            add_action('wp_enqueue_scripts', array($this, 'register_my_scripts'));
        }

//        add_action( 'switch_theme', 'flush_rewrite_rules', 15 );
    }

    public function register_my_scripts(){
        wp_enqueue_style('advanced-product', AP_Functions::get_my_url().'/assets/css/style.css');
        wp_enqueue_style('advanced-product');
    }


    public function add_query_vars_filter( $vars ) {

        $post_type  = isset($_REQUEST['post_type'])?$_REQUEST['post_type']:get_post_type();

        if($post_type != 'ap_product'){
            return $vars;
        }

        $vars[] = 'field';

        return $vars;

    }

    public function change_event_posts_per_page( $query ) {

//        if (! $query->is_main_query() || !isset($query->query['post_type']) ||
//            (! is_post_type_archive('ap_product') &&
//            ( isset($query->query['post_type']) && 'ap_product' != $query->query['post_type'] ))) {
//            return $query;
//        }

//        var_dump(is_post_type_archive('ap_product'));

//        if ( !is_admin() && $query->is_main_query()) {
        if ( !is_admin() && is_post_type_archive('ap_product') && $query->is_main_query()) {

//            $query_var  = \get_query_var('field');
            $query_var  = isset($_GET['field'])?$_GET['field']:array();
            if(empty($query_var)){
                return $query;
            }

//            $query->set('post_type', array('ap_product'));

            $meta_query = $query->get('meta_query');
            $meta_query = !empty($meta_query)?$meta_query:array();
//            $meta_query = array();

            global $wpdb;
            foreach ($query_var as $fname => $query_value){
                if (empty($query_value)) {
                    continue;
                }
                $field  = AP_Custom_Field_Helper::get_custom_field($fname);
                if(!empty($field)){
                    $acf_attr   = AP_Custom_Field_Helper::get_custom_field_option_by_id($field->ID);
                    if(is_array($query_value)){
                        $submeta_query  = array();
                        $submeta_query['relation']    = 'OR';
                        foreach ($query_value as $qval) {
                            $qval   = serialize($qval);
                            $qval   = preg_replace('/(^[a-z]+:[0-9]+:\{)|(\}$)/', '', $qval);
//                            $qval   = addslashes($qval);
                            $submeta_query[] = array(
                                'key' => $acf_attr['name'],
                                'value' => $qval,
                                'compare' => 'LIKE',
                            );
                        }
                        $meta_query[]   = $submeta_query;
                    }else{
//                    if(!is_array($query_value)){
                        $meta_query[] = array(
                            'key' => isset($acf_attr['name'])?$acf_attr['name']:'',
                            'value' => $query_value,
                            'compare' => '=',
                        );
                    }
                }
            }

            $query->set('meta_query', $meta_query);
        }
    }

    public function get_search_form( $shortcode_atts ) {
        $defaults = array(
            'include' 	=> false,
            'exclude'   => '',
            'action' 	=> get_post_type_archive_link( 'ap_product' ), // action url,
            'form'		=> 'true',
            'button' 	=> __( 'Search', 'advanced-product'),
            'form_atts' => '',
            'submit_text' => '',
            'submit_icon' => '',
            'submit_icon_position' => 'before',
            'enable_keyword'    => true,
            'enable_ajax'       => false,
            'instant'           => false,
            'update_url'        => false,
            'show_label'        => true,
            'column'            => 1,
            'column_large'      => 1,
            'column_laptop'     => 1,
            'column_tablet'     => 1,
            'column_mobile'     => 1,
        );

        extract( shortcode_atts( apply_filters( 'advanced-product/search-form/defaults', $defaults ), $shortcode_atts ) );

        $show_label     = filter_var($show_label, FILTER_VALIDATE_BOOLEAN);
        $enable_ajax = filter_var($enable_ajax, FILTER_VALIDATE_BOOLEAN);
        $enable_keyword = filter_var($enable_keyword, FILTER_VALIDATE_BOOLEAN);

        if(isset($include)){
            if(!empty($include)) {
                $include    = explode(',', $include);
                $fields     = AP_Custom_Field_Helper::get_acf_fields($include);
            }else{
                $fields = AP_Custom_Field_Helper::get_acf_fields_by_display_flag('show_in_search');
            }
        }
      else{
            $fields = AP_Custom_Field_Helper::get_acf_fields_by_display_flag('show_in_search');
        }

        if(!empty($fields)) {
            require __DIR__ . '/tpl/search.php';
        }
    }
}