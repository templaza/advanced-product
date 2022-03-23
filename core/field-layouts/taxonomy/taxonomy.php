<?php

namespace Advanced_Product\Field\Layout;

use Advanced_Product\Field_Layout;

defined('ADVANCED_PRODUCT') or exit();

class Taxonomy extends Field_Layout {

    public function hooks(){
        parent::hooks();
        add_filter( 'advanced-product/field/value_html/type='.$this -> get_name(), array($this, 'value_html_filter'), 10, 4 );
    }

    public function value_html_filter($html, $value, $field, $post_field){
        $term   = null;
        if(is_object($value)){
            $term   = $value;
        }elseif(is_array($value)){
            foreach ($value as $val){
                if(is_numeric($val)) {
                    $term   = get_term_by('term_id', $val, $field['taxonomy']);
                    $html   = $term -> post_title;
                }else{
                    $term   = get_term_by('slug', $val, $field['taxonomy']);
                }
            }
        }elseif(is_numeric($value)){
            $term   = get_term_by('term_id', $value, $field['taxonomy']);

        }else{
            $term   = get_term_by('slug', $value, $field['taxonomy']);
        }

        if(!empty($term) && !is_wp_error($term)) {
            $html = $term->name;
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

new Taxonomy();