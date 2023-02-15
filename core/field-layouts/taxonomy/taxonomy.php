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

        $file   = $this -> _get_html_value_path();

        if(file_exists($file)){
            ob_start();
            require $file;
            $html   = ob_get_contents();
            ob_end_clean();
        }

        return $html;
    }
}

new Taxonomy();