<?php

namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

class AP_Templates{

    protected static $cache = array();

    public static function locate_my_template($template_names, $load = false, $require_once = true,
                                              $args = array(), $file_type = '.php'){
        $located        = '';
        $base           = ADVANCED_PRODUCT.'/templates';
        $framework_path = ADVANCED_PRODUCT_TEMPLAZA_FRAMEWORK_TEMPLATE_PATH;

        foreach ( (array) $template_names as &$template_name ) {
            if ( ! $template_name ) {
                continue;
            }
            $framework_name = $template_name;
            $template_name  = $base.'/'.$template_name;

            $file_type_reg  = addcslashes($file_type, '.');

            if(!preg_match('/'.$file_type_reg.'$/i', $template_name)){
                $template_name  .= $file_type;
            }
            if(!preg_match('/'.$file_type_reg.'$/i', $framework_name)){
                $framework_name  .= $file_type;
            }
            if ( file_exists( get_stylesheet_directory() . '/' . $template_name ) ) {
                $located = get_stylesheet_directory() . '/' . $template_name;
                break;
            } elseif ( file_exists( get_template_directory() . '/' . $template_name ) ) {
                $located = get_template_directory() . '/' . $template_name;
                break;
            } elseif ( file_exists( $framework_path.'/'.$framework_name ) ) {
                $located   = $framework_path.'/'.$framework_name;
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

    public static function load_my_layout($partial, $load = true, $require_once = false, $args = array(), $file_type = '.php'){
        $partial    = str_replace('.', '/', $partial);
        $located    = self::locate_my_template((array) $partial, $load, $require_once, $args, $file_type);

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
