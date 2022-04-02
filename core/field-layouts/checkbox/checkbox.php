<?php

namespace Advanced_Product\Field\Layout;

use Advanced_Product\Field_Layout;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\Field\Layout\Checkbox')){
    class CheckBox extends Field_Layout {

        public function render(){

        }

//        public function render_form($field){
//            $file   = __DIR__.'/tpl/form.php';
//            if(file_exists($file)) {
//                require __DIR__ . '/tpl/form.php';
//            }
//        }

        /*
        *
        *  Create extra options for your field. This is rendered when editing a field.
        *  The value of $field['name'] can be used (like bellow) to save extra data to the $field
        *
        *  @param	$field	- an array holding all the field's data
        */
        public function render_search_settings( $field )
        {
            $field['s_type']                = isset($field['s_type'])?$field['s_type']:$field['type'];
            $field['s_choices']             = isset($field['s_choices'])?$field['s_choices']:'';
            $field['s_default_value']       = isset($field['s_default_value'])?$field['s_default_value']:'';
            $field['s_meta_query_compare']  = isset($field['s_meta_query_compare'])?$field['s_meta_query_compare']:'';

            $key = $field['name'];

            // implode choices so they work in a textarea
            if( is_array($field['choices']) )
            {
                foreach( $field['choices'] as $k => $v )
                {
                    $field['choices'][ $k ] = $k . ' : ' . $v;
                }
                $field['choices'] = implode("\n", $field['choices']);
            }

            $file   = __DIR__.'/tpl/search_settings.php';
            if(file_exists($file)) {
                require $file;
            }

        }
    }
}

new CheckBox();