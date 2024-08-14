<?php

namespace Advanced_Product\Field\Layout;

use Advanced_Product\Field_Layout;

defined('ADVANCED_PRODUCT') or exit();

class Repeater extends Field_Layout{

    public function hooks()
    {
        parent::hooks();

        add_filter( 'advanced-product/field/value_html/type='.$this -> get_name(), array($this, 'value_html'), 10, 4 );
    }


    public function value_html($html, $value, $field, $post_field){
        $file   = $this -> _get_html_value_path();
        if(file_exists($file)){
            ob_start();
            require $file;
            $html   = ob_get_contents();
            ob_end_clean();
        }
        return $html;
    }

    public function render(){

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

//    /*
//    *
//    *  Create extra options for your field. This is rendered when editing a field.
//    *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
//    *
//    *  @param	$field	- an array holding all the field's data
//    */
//    public function render_search_settings( $field )
//    {
//        $field['s_type']            = isset($field['s_type'])?$field['s_type']:$field['type'];
//        $field['s_choices']         = isset($field['s_choices'])?$field['s_choices']:'';
//        $field['s_default_value']   = isset($field['s_default_value'])?$field['s_default_value']:'';
//
//        $key = $field['name'];
//
//        $file   = __DIR__.'/tpl/search_settings.php';
//        if(file_exists($file)) {
//            require $file;
//        }
//
//    }
}
new Repeater();