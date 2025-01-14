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
        add_action('advanced-product/compare/before_content', array($this, 'before_content'));

        add_action('wp_ajax_advanced-product/shortcode/advanced-product/compare-list', array($this, 'render_compare_list'));
        add_action('wp_ajax_nopriv_advanced-product/shortcode/advanced-product/compare-list', array($this, 'render_compare_list'));

        add_action('wp_ajax_advanced-product/shortcode/advanced-product/quick-view', array($this, 'render_quickview'));
        add_action('wp_ajax_nopriv_advanced-product/shortcode/advanced-product/quick-view', array($this, 'render_quickview'));

	    add_action('wp_ajax_advanced_autocomplete_search', array($this, 'advanced_autocomplete_search'));
	    add_action('wp_ajax_nopriv_advanced_autocomplete_search', array($this, 'advanced_autocomplete_search'));
    }

    public function before_content(){
        add_action('advanced-product/archive/compare/action', array($this, 'render_compare_action'), 10, 2);
    }

    public function render_compare_action($pid, $args){
        $compare_actions = array();
        $compare_action = apply_filters('advanced-product/compare/action', '', $this);
        $compare_action = !empty($compare_action)?trim($compare_action):'';

        if(!empty($compare_action)){
            array_push($compare_actions, $compare_action);
        }
        $compare_actions = apply_filters('advanced-product/compare/actions', $compare_actions, $this);

        AP_Templates::load_my_layout('shortcodes.'. $this -> get_shortcode_name().'.compare-action', true, false,
            array('actions' => $compare_actions, 'show_compare_button' => false, 'pid' => $pid));
    }

    public function display_footer(){
        AP_Templates::load_my_layout('shortcodes.'. $this -> get_shortcode_name().'.compare-modal', true, false, array(), '.tpl.php');
        AP_Templates::load_my_layout('shortcodes.'. $this -> get_shortcode_name().'.quickview-modal', true, false, array(), '.tpl.php');
        AP_Templates::load_my_layout('shortcodes.'. $this -> get_shortcode_name().'.compare-preloader', true, false, array(), '.tpl.php');
        AP_Templates::load_my_layout('shortcodes.'. $this -> get_shortcode_name().'.compare-list-button', true, false, array(), '.php');
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
                array('products' => $products, 'show_archive_compare_button' => false));
            $content    = ob_get_contents();
        ob_end_clean();

        wp_reset_query();


        echo wp_send_json_success($content);
        wp_die();
    }

    public function render_quickview(){
        $pid   = isset($_POST['pid'])?$_POST['pid']:false;

        if(!$pid){
            return '';
        }

        wp_reset_query();
        $product   = AP_Product_Helper::get_products(array(
            'p' => $pid));

        if(is_wp_error($product) || empty($product)){
            echo wp_send_json_error(__('Can not found product', 'advanced-product'));
            wp_die();
        }

        if($product -> have_posts()) {
            \ob_start();
            while($product -> have_posts()) {
                $product -> the_post();
                    AP_Templates::load_my_layout('shortcodes.' . $this->get_shortcode_name() . '.quickview', true, false,
                        array('product' => $product, 'show_archive_quickview_button' => false));
                break;
            }
            $content = ob_get_contents();
            ob_end_clean();
        }

        wp_reset_query();


        echo wp_send_json_success($content);
        wp_die();
    }

	public function advanced_autocomplete_search(){

		if ( empty( $_REQUEST['title'] ) ) {
			wp_die();
		}

		// WP Query arguments

		// we get the 'term' from the ajax call, clean it and make a search
		$args = array(
			's'         => trim( esc_attr( strip_tags( $_REQUEST['title'] ) ) ),
			'post_type' => 'ap_product',
			'posts_per_page' => 10
		);

		// array to keep results
		$results = array();

		// make a query
		$query = new \WP_Query( $args );

		// save results
		// formatted with the title as 'label' for the autocomplete script
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$results[] = array(
					'label'     => esc_html( get_the_title() ),    // title
					'link'      => get_permalink(),                // link
					'id'        => get_the_ID(),                   // id
					// and whatever eles you want to send to the front end
				);

			}
		}
		wp_reset_postdata();

		// echo results
		echo json_encode($results);

		// kill process
		// all ajax actions in WP need to die when they are done!
		wp_die();
	}

    public function get_shortcode_name()
    {
        $name   = parent::get_shortcode_name();

        return str_replace('_', '-', $name);
    }

    public function render($atts){
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
        wp_enqueue_style('jquery-ui-autocomplete');
        wp_enqueue_script('advanced-product');
        wp_enqueue_script('advanced-product-serialize-object');
        wp_localize_script('advanced-product', 'advanced_product', array(
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'l10n' => array(
                'compare' => array(
                    'text'                      => __('Add to compare', 'advanced-product'),
                    'active_text'               => __('In compare list', 'advanced-product'),
                    'delete_question'           => __('Do you want to remove this product from compare list?', 'advanced-product'),
                    'add_product_successfully'  => __('Add product to compare list successfully', 'advanced-product'),
                )
            )
        ));
    }

}