<?php

namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

class AP_Templates{

    protected static $cache = array();

    public static function locate_my_template($template_names, $load = false, $require_once = true, $args = array()){
        $located        = '';
        $base           = ADVANCED_PRODUCT.'/templates';
        $framework_path = ADVANCED_PRODUCT_TEMPLAZA_FRAMEWORK_TEMPLATE_PATH;

        foreach ( (array) $template_names as &$template_name ) {
            if ( ! $template_name ) {
                continue;
            }
            $template_name  = $base.'/'.$template_name;
            if(!preg_match('/\.php$/i', $template_name)){
                $template_name  .= '.php';
            }
            if ( file_exists( get_stylesheet_directory() . '/' . $template_name ) ) {
                $located = get_stylesheet_directory() . '/' . $template_name;
                break;
            } elseif ( file_exists( get_template_directory() . '/' . $template_name ) ) {
                $located = get_template_directory() . '/' . $template_name;
                break;
            } elseif ( file_exists( $framework_path.'/'.$template_name ) ) {
                $located   = $framework_path.'/'.$template_name;
                break;
            } elseif ( file_exists( ADVANCED_PRODUCT_PLUGIN_DIR_PATH.'/'.$template_name ) ) {
                $located   = ADVANCED_PRODUCT_PLUGIN_DIR_PATH.'/'.$template_name;
                break;
            }
        }

        if($load && $located != '') {
            load_template($located, $require_once, $args);
        }

        return $located;
    }

    public static function load_my_layout($partial, $load = true, $require_once = false, $args = array()){
        $partial    = str_replace('.', '/', $partial);
        $located    = self::locate_my_template((array) $partial, $load, $require_once, $args);

        return $located;
    }

    public static function load_my_header($name = null, $require_once = true, $args = array()){
        if('' != $name){
            self::load_my_layout($name, true, $require_once, $args);
        }else {
            self::load_my_layout('header', true, $require_once, $args);
        }
    }
    public static function load_my_footer($name = 'footer', $args = array()){
        if('' != $name){
            self::load_my_layout($name, true, $args);
        }else {
            self::load_my_layout('footer', true, $args);
        }
    }
}
