<?php

namespace Advanced_Product\Field\Layout;

use Advanced_Product\AP_Functions;
use Advanced_Product\Base;
use Advanced_Product\Field_Layout;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\Field\Layout\Select')){
    class Select extends Field_Layout{

        protected $field    = array();
        protected $group    = array();

        public function hooks(){
            parent::hooks();

            add_action( 'admin_enqueue_scripts', array($this, 'admin_select_enqueue_script') );
        }

        public function admin_select_enqueue_script(){
            global $post_type;

            if(is_admin() && $post_type == 'ap_custom_field') {
                wp_register_script('advanced-product__fields-layout-select', AP_Functions::get_my_url()
                    . '/core/field-layouts/select/select.js');
                wp_enqueue_script('advanced-product__fields-layout-select', array('advanced-product'));
            }
        }

        public function render(){
        }

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
            $field['s_from_to']             = isset($field['s_from_to'])?$field['s_from_to']:0;
            $field['s_choices_to']          = isset($field['s_choices_to'])?$field['s_choices_to']:'';
            $field['s_choices_from']        = isset($field['s_choices_from'])?$field['s_choices_from']:'';
            $field['s_default_value']       = isset($field['s_default_value'])?$field['s_default_value']:'';
            $field['s_default_value_to']    = isset($field['s_default_value_to'])?$field['s_default_value_to']:'';
            $field['s_default_value_from']  = isset($field['s_default_value_from'])?$field['s_default_value_from']:'';
            $field['s_meta_query_compare']  = isset($field['s_meta_query_compare'])?$field['s_meta_query_compare']:'';

            $key = $field['name'];

//            // implode choices so they work in a textarea
//            if(isset($field['choices']) && is_array($field['choices']) )
//            {
//                foreach( $field['choices'] as $k => $v )
//                {
//                    $field['choices'][ $k ] = $k . ' : ' . $v;
//                }
//                $field['choices'] = implode("\n", $field['choices']);
//            }

            $file   = __DIR__.'/tpl/search_settings.php';
            if(file_exists($file)) {
                require $file;
            }

        }

        public function update_field($field, $post_id){

//            var_dump( $_POST);
//            var_dump($field);
//            die(__METHOD__);
        }
//
//        public function load_field_defaults($field){
////            $field['search_type']   = '';
////            var_dump($field); die(__METHOD__);
//            return $field;
//
////            var_dump($field); die(__METHOD__);
//        }

        public function admin_enqueue_scripts(){
            global $post_type;
        }
    }
}

new Select();