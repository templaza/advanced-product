<?php

namespace Advanced_Product\Field\Layout;

use Advanced_Product\Field_Layout;

defined('ADVANCED_PRODUCT') or exit();

class Gallery extends Field_Layout {

    public function hooks(){
        parent::hooks();

//        add_action( 'admin_enqueue_scripts', array($this, 'admin_select_enqueue_script') );
//        add_action( 'advanced-product/field/value_html/type='.$this -> get_name(), array($this, 'value_html'), 10, 3 );
        add_filter( 'advanced-product/field/value_html/type='.$this -> get_name(), array($this, 'value_html_filter'), 10, 4 );
    }

    public function value_html($value, $field, $post_field){
        $path       = \ADVANCED_PRODUCT_FIELD_LAYOUT_PATH.'/'.$this -> get_name().'/tpl';
        $theme_path = \ADVANCED_PRODUCT_THEME_TEMPLATE_PATH.'/field-layouts/'.$this -> get_name();

        $layout     = 'default.php';

        $file   = $theme_path.'/'.$layout;
        if(!file_exists($file)){
            $file   = $path.'/'.$layout;
        }

//        $html   = '';
        if(file_exists($file)){
//            ob_start();
            require $file;
//            $html   = ob_get_contents();
//            ob_end_clean();
        }
//        return $html;
//        var_dump($file);
////        if()
////        var_dump($value);
//        var_dump($path);
//        var_dump($theme_path);
//        var_dump(__FILE__);
//
////        die(__METHOD__);
    }

    public function value_html_filter($html, $value, $field, $post_field){
        $file   = $this -> _get_html_value_path();
        if(file_exists($file)){
            ob_start();
            require $file;
            $html   = ob_get_contents();
            ob_end_clean();
        }
        return $html;
    }

    protected function _get_html_value_path($layout = 'default'){
        $path       = ADVANCED_PRODUCT_FIELD_LAYOUT_PATH.'/'.$this -> get_name().'/tpl';
        $theme_path = ADVANCED_PRODUCT_THEME_TEMPLATE_PATH.'/field-layouts/'.$this -> get_name();

        $layout     = !preg_match('/\.php$/',$layout)?$layout.'.php':$layout;

        $file   = $theme_path.'/'.$layout;
        if(!file_exists($file)){
            $file   = $path.'/'.$layout;
        }

        if(file_exists($file)){
            return $file;
        }

        return false;
    }
}

new Gallery();