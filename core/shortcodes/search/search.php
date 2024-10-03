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
    }

    public function hooks(){
        add_shortcode( 'advanced-product-form', array( $this, 'get_search_form' ) );

        if(!is_admin()) {
            add_filter('query_vars', array($this, 'add_query_vars_filter'));
            add_action('pre_get_posts', array($this, 'change_event_posts_per_page'), 20);

            add_action('wp_enqueue_scripts', array($this, 'register_my_scripts'));
        }
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

        if ( !is_admin() && is_post_type_archive('ap_product') && $query->is_main_query()) {

            $query_var  = isset($_GET['field'])?$_GET['field']:array();
            if(empty($query_var)){
                return $query;
            }

            $meta_query = $query->get('meta_query');
            $meta_query = !empty($meta_query)?$meta_query:array();

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

                            $query_filter    = array_filter($query_value);
                        }

                        foreach ($query_value as $i => $qval) {
                            if($type == 'number') {
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
                    }else{
                        switch($type){
                            default:
                                $meta_main[$acf_attr['key']] = array(
                                    'key' => isset($acf_attr['name']) ? $acf_attr['name'] : '',
                                    'value' => $query_value,
                                    'compare' => 'LIKE',
                                );
                                break;
                            case 'number':
                                $meta_main[$acf_attr['key']] = array(
                                    'key' => $f_name,
                                    'value' => (float) $query_value,
                                    'compare' => '=',
                                    'type' => 'DECIMAL(10,3)'
                                );
                                break;
                            case 'date_picker':
							
                                $replace = array(
                                    'd' => 'dd',
                                    'D' => 'D',
                                    'j' => 'd',
                                    'l' => 'DD',
                                    'N' => '',
                                    'S' => '',
                                    'w' => '',
                                    'z' => 'o',
                                    // Week
                                    'W' => '',
                                    // Month
                                    'F' => 'MM',
                                    'm' => 'mm',
                                    'M' => 'M',
                                    'n' => 'm',
                                    't' => '',
                                    // Year
                                    'L' => '',
                                    'o' => '',
                                    'Y' => 'yy',
                                    'y' => 'y',
                                    // Time
                                    'a' => '',
                                    'A' => '',
                                    'B' => '',
                                    'g' => '',
                                    'G' => '',
                                    'h' => '',
                                    'H' => '',
                                    'i' => '',
                                    's' => '',
                                    'u' => ''
                                );

                                if(strpos($acf_attr['date_format'], 'mm') != false){
                                    unset($replace['n']);
                                }
                                if(strpos($acf_attr['date_format'], 'dd') != false){
                                    unset($replace['j']);
                                }
                                $date_format = str_replace(array_values($replace),array_keys($replace) , $acf_attr['date_format']);

                                $meta_main[$acf_attr['key']]   = array(
                                    'key' => $f_name,
                                    'value' => date_i18n($date_format, strtotime($query_value)),
                                    'compare' => '=',
                                );
                                break;
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
            'taxonomy_display'  => '',
            'max_height'        => '',
        );

        $inventory_page_id  = get_field('ap_inventory_page_id', 'option');
        if(is_archive() || ($inventory_page_id && is_page($inventory_page_id))) {
            $defaults['action'] = get_post_type_archive_link( 'ap_product' );
        }

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