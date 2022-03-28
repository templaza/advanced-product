<?php
namespace Advanced_Product\Shortcode;

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
            add_action('pre_get_posts', array($this, 'change_event_posts_per_page'));

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

        $fields = AP_Custom_Field_Helper::get_fields_by_display_flag('show_in_search');
        if ($fields) {
            foreach ($fields as $field) {
                $acf_attr = AP_Custom_Field_Helper::get_custom_field_option_by_id($field->ID);
                $vars[] = 'field['.$acf_attr['name'].']';
            }
        }

        $vars[] = 'field';

        return $vars;

    }

    public function change_event_posts_per_page( $query ) {

        if (! $query->is_main_query() || !isset($query->query['post_type']) ||
            (! is_post_type_archive('ap_product') &&
            ( isset($query->query['post_type']) && 'ap_product' != $query->query['post_type'] ))) {
            return;
        }

        if ( !is_admin() ) {

            $query_var  = \get_query_var('field');
            if(empty($query_var)){
                return;
            }

//            $query->set('post_type', array($query->query['post_type']));
            $query->set('post_type', array('ap_product'));

            $meta_query = array();

            $fields = AP_Custom_Field_Helper::get_fields_by_display_flag('show_in_search');
            if ($fields) {

                foreach ($fields as $field) {
                    $acf_attr = AP_Custom_Field_Helper::get_custom_field_option_by_id($field->ID);

                    $vars           = array_keys($query_var);

                    $query_value    = isset($query_var[$acf_attr['name']])?$query_var[$acf_attr['name']]:'';

                    if (empty($query_value) || !in_array($acf_attr['name'], $vars)) {
                        continue;
                    }

                    if (is_array($query_value)) {
                        $query_value  = array_filter($query_value);
                    }

                    if(!empty($query_value)){
                        if(isset($acf_attr['s_meta_query_compare'])){
                            $meta_query[] = array(
                                'key' => $acf_attr['name'],
                                'value' => $query_value,
                                'compare' => $acf_attr['s_meta_query_compare'],
                            );
                        }else{
                            if (isset($acf_attr['multiple']) && $acf_attr['multiple']) {
                                $meta_query[] = array(
                                    'key' => $acf_attr['name'],
                                    'value' => $query_value,
                                    'compare' => 'IN'
                                );
                            } elseif (is_numeric($query_value)) {
                                $meta_query[] = array(
                                    'key' => $acf_attr['name'],
                                    'value' => $query_value,
                                    'type' => 'numeric',
                                    'compare' => '='
                                );
                            } elseif (is_array($query_value)) {
                                if(isset($query_value['min']) && isset($query_value['max'])){
                                    $meta_query[] = array(
                                        'key' => $acf_attr['name'],
                                        'value' => array_values($query_value),
                                        'compare' => 'BETWEEN'
                                    );
                                }else {
                                    $meta_query[] = array(
                                        'key' => $acf_attr['name'],
                                        'value' => $query_value,
                                        'compare' => 'IN'
                                    );
                                }
                            } else {
                                $meta_query[] = array(
                                    'key' => $acf_attr['name'],
                                    'value' => $query_value,
                                    'compare' => 'LIKE'
                                );
                            }
                        }
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
            'button' 	=> __( 'Search', $this -> text_domain),
            'form_atts' => '',
            'submit_text' => '',
            'submit_icon' => '',
            'submit_icon_position' => 'before',
            'enable_keyword' => true,
            'show_label' => true
        );
        extract( shortcode_atts( apply_filters( 'advanced-product/search-form/defaults', $defaults ), $shortcode_atts ) );

        $show_label     = filter_var($show_label, FILTER_VALIDATE_BOOLEAN);
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