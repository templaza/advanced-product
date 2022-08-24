<?php

namespace Advanced_Product\Shortcode;

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Product_Helper;
use Advanced_Product\ShortCodeAP;
use Advanced_Product\AP_Templates;

defined('ADVANCED_PRODUCT') or exit();

class Advanced_ProductSCAP extends ShortCodeAP {

    public function hooks()
    {
        parent::hooks();

        add_action( 'wp_enqueue_scripts', array($this, 'wp_enqueue_scripts' ));

        add_action('wp_footer', array($this, 'display_footer'));
        add_action('advanced-product/shortcodes/advanced-product/compare/before_content', array($this, 'before_content'));

        add_action('wp_ajax_advanced-product/shortcode/advanced-product/compare-list', array($this, 'render_compare_list'));
        add_action('wp_ajax_nopriv_advanced-product/shortcode/advanced-product/compare-list', array($this, 'render_compare_list'));
    }

    public function before_content(){
        add_action('advanced-product/archive/after_content', array($this, 'render_compare_action'));
    }

    public function render_compare_action(){
        AP_Templates::load_my_layout('shortcodes.'. $this -> get_shortcode_name().'.compare-action');
    }

    public function display_footer(){
        AP_Templates::load_my_layout('shortcodes.'. $this -> get_shortcode_name().'.compare-modal', true, false, array(), '.tpl.php');
        AP_Templates::load_my_layout('shortcodes.'. $this -> get_shortcode_name().'.compare-preloader', true, false, array(), '.tpl.php');
    }

    public function render_compare_list(){
        $pids   = isset($_POST['pid'])?(array) $_POST['pid']:false;

        if(!$pids){
            return '';
        }

        $products   = AP_Product_Helper::get_products(array(
            'post__in' => $pids));

        ob_start();
            AP_Templates::load_my_layout('shortcodes.'. $this -> get_shortcode_name().'.compare-list', true, false,
                array('products' => $products));
            $content    = ob_get_contents();
        ob_end_clean();

        wp_reset_query();


        echo wp_send_json_success($content);
        wp_die();
    }

    public function get_shortcode_name()
    {
        $name   = parent::get_shortcode_name();

        return str_replace('_', '-', $name);
    }

    public function render($atts){
//        wp_enqueue_script('advanced-product');
//        wp_localize_script('advanced-product', 'advanced_product', array(
//            'ajaxurl'   => admin_url('admin-ajax.php'),
//            'l10n' => array(
//                'compare' => array(
//                    'active_text'   => __('In compare list', $this -> text_domain),
//                    'add_product_successfully'   => __('Add product to compare list successfully', $this -> text_domain),
//                )
//            )
//        ));

        $layout = $this -> get_shortcode_name();

        if(isset($atts['type']) && !empty($atts['type'])){
            $layout.= '.'.$atts['type'];
        }

        ob_start();
        AP_Templates::load_my_layout('shortcodes.'.$layout, true, false, array(
            'atts'  => $atts
        ));
        $content    = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    public function wp_enqueue_scripts(){
        wp_enqueue_script('advanced-product');
        wp_localize_script('advanced-product', 'advanced_product', array(
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'l10n' => array(
                'compare' => array(
                    'active_text'   => __('In compare list', $this -> text_domain),
                    'add_product_successfully'   => __('Add product to compare list successfully', $this -> text_domain),
                )
            )
        ));
    }

}