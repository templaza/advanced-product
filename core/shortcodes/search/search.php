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
                    $acf_attr   = AP_Custom_Field_Helper::get_custom_field_option_by_id($field->ID,
                        array('exclude_core_field' => false));

                    $type       = isset($acf_attr['type'])?$acf_attr['type']:'';
                    $f_type     = isset($acf_attr['field_type'])?$acf_attr['field_type']:'';
                    $f_name     = isset($acf_attr['name']) ? $acf_attr['name'] : '';
                    $meta_main  = array();

                    if(is_array($query_value)){
                        $submeta_query = array();
                        $submeta_query['relation'] = 'OR';
                        if($type == 'number') {
//                            $submeta_query['relation'] = 'AND';

                            $query_filter    = array_filter($query_value);
                        }

                        foreach ($query_value as $i => $qval) {
                            if($type == 'number') {
//                                if(!empty($qval)){
                                if($qval !== ''){
                                    if(isset($query_filter) && is_array($query_filter)
                                        && count($query_filter) == 1 && $i % 2 == 0){
                                        $submeta_query[] = array(
                                            'key' => $acf_attr['name'],
                                            'value' => $qval,
                                            'compare' => '>=',
                                            'type' => 'DECIMAL(10,3)'
                                        );
                                    }else {
                                        $submeta_query[] = array(
                                            'key' => $acf_attr['name'],
                                            'value' => $query_value,
                                            'compare' => 'BETWEEN',
                                            'type' => 'DECIMAL(10,3)'
                                        );
                                    }
                                }
                            }else {
                                $orgval = $qval;

                                if(in_array($f_type, array('checkbox', 'multi_select'))) {
                                    $qval = serialize($qval);
                                    $qval = preg_replace('/(^[a-z]+:[0-9]+:\{)|(\}$)/', '', $qval);
                                }

                                $qval   = apply_filters('advanced-product/shortcodes/search-form/meta_query/pre-filter-value',
                                    $qval, $orgval, $acf_attr, $field);
                                $qval   = apply_filters('advanced-product/shortcodes/search-form'.$type
                                    .'/meta_query/pre-filter-value', $qval, $orgval, $acf_attr, $field);

                                $submeta_query[] = array(
                                    'key' => $acf_attr['name'],
                                    'value' => $qval,
                                    'compare' => 'LIKE',
                                );
                            }
                        }

                        $meta_main[] = $submeta_query;
//                        if($type == 'number'){
//                            $query -> set('orderby', array($f_name => 'ASC'));
//                        }
                    }else{
                        if($type == 'number'){
                            $meta_main[$acf_attr['key']] = array(
                                'key' => $f_name,
                                'value' => (float) $query_value,
                                'compare' => '=',
                                'type' => 'DECIMAL(10,3)'
                            );
//                            $query -> set('orderby', array($f_name => 'ASC'));
                        }else {
                            $meta_main[$acf_attr['key']] = array(
                                'key' => isset($acf_attr['name']) ? $acf_attr['name'] : '',
                                'value' => $query_value,
                                'compare' => 'LIKE',
                            );
                        }
                    }

                    // Hook to prepare our meta query
                    $meta_main  = apply_filters('advanced-product/search-form/meta_query', $meta_main, $acf_attr, $field);

                    if(!empty($meta_main)){
                        $meta_query = array_merge($meta_query, array_values($meta_main));
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
            'limit_height'    => true,
            'enable_ajax'       => false,
            'instant'           => false,
            'update_url'        => false,
            'show_label'        => true,
            'column'            => 1,
            'column_large'      => 1,
            'column_laptop'     => 1,
            'column_tablet'     => 1,
            'column_mobile'     => 1,
            'max_height'        => '',
        );

        extract( shortcode_atts( apply_filters( 'advanced-product/search-form/defaults', $defaults ), $shortcode_atts ) );

        $show_label     = filter_var($show_label, FILTER_VALIDATE_BOOLEAN);
        $enable_ajax = filter_var($enable_ajax, FILTER_VALIDATE_BOOLEAN);
        $enable_keyword = filter_var($enable_keyword, FILTER_VALIDATE_BOOLEAN);
        $limit_height = filter_var($limit_height, FILTER_VALIDATE_BOOLEAN);

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