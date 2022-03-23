<?php

namespace Advanced_Product\Field\Layout;

use Advanced_Product\Field_Layout;

defined('ADVANCED_PRODUCT') or exit();

class Image extends Field_Layout {

    public function hooks(){
        parent::hooks();
        add_filter( 'advanced-product/field/value_html/type='.$this -> get_name(), array($this, 'value_html_filter'), 10, 4 );
    }

    public function value_html($value, $field, $post_field){
        $path       = ADVANCED_PRODUCT_FIELD_LAYOUT_PATH.'/'.$this -> get_name().'/tpl';
        $theme_path = ADVANCED_PRODUCT_THEME_TEMPLATE_PATH.'/field-layouts/'.$this -> get_name();

        $layout     = 'default.php';

        $file   = $theme_path.'/'.$layout;
        if(!file_exists($file)){
            $file   = $path.'/'.$layout;
        }

        if(file_exists($file)){
            require $file;
        }
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

new Image();