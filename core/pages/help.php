<?php

namespace Advanced_Product\Page;

use Advanced_Product\Application;
use Advanced_Product\Base;
use Advanced_Product\Page;
use Advanced_Product\Install;
use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\FieldHelper;

defined('ADVANCED_PRODUCT') or exit();

class Help extends Page {

    protected $build_in;

//    public function __construct($core = null, $post_type = null)
//    {
//        parent::__construct($core, $post_type);
//
//    }

    public function hooks()
    {
        parent::hooks();

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        add_action('wp_ajax_advanced-product/page/help/install-sample-data', array($this, 'install_sample_data'));
        add_action('wp_ajax_nopriv_advanced-product/page/help/install-sample-data', array($this, 'install_sample_data'));
//        add_action( 'plugins_loaded', array( $this, 'register_admin_fields' ) );
    }

    public function register()
    {
        $register   = parent::register();

        $register['menu_title'] = __('Help', 'advanced-product');
        $register['position']   = 15;

        return $register;
    }

    public function install_sample_data(){
        $success    = false;

        if(class_exists('Advanced_Product\Install')) {
            $install = new Install();
            if(method_exists($install, 'install')){
                $success = call_user_func(array($install, 'install'));
            }
        }

        $mtype  = 'success';
        $result = array(
            'reload'    => true,
            'success'   => $success,
            'message'   => __('Installed sample data successfully!', 'advanced-product'),
        );

        if(!$success){
            $mtype              = 'error';
            $result['message']  = __('Can not install sample data', 'advanced-product');
        }

        $app    = Application::get_instance();
        $app -> enqueue_message($result['message'], $mtype);

        echo wp_json_encode($result);

        wp_die();
    }

    public function admin_enqueue_scripts($hook){
        if(is_admin() && $hook === 'ap_product_page_'.$this -> get_page_name()) {
            if(!wp_script_is('templaza-framework_uikit_js')) {
                wp_enqueue_script('advanced-product__js_uikit');
                wp_enqueue_script('advanced-product__js_uikit-icons');
            }
            wp_localize_script('advanced-product', 'ap_help_page', array(
                'i18nStrings' => array(
                    'sample_data_confirm_question'  => __('Are you sure want to install sample data?', 'templaza-framework')
                )
            ));
        }
    }

}