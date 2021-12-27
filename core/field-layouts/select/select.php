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

        public function __construct($field = array(), $group = array())
        {
            parent::__construct($field, $group);

            if(is_admin()) {
                wp_register_script('advanced-product__fields-layout-select', AP_Functions::get_my_url()
                    . '/core/field-layouts/select/select.js');
            }
        }

        public function hooks(){
            parent::hooks();

            add_action( 'admin_enqueue_scripts', array($this, 'admin_select_enqueue_script') );



//            add_action( 'admin_print_scripts-post-new.php', array($this, 'admin_select_enqueue_script') );
//            add_action( 'admin_print_scripts-post.php', array($this, 'admin_select_enqueue_script'));
        }

        public function admin_select_enqueue_script(){
            global $post_type;

            if($post_type == 'ap_custom_field') {
                wp_enqueue_script('advanced-product__fields-layout-select', array('advanced-product'));
            }
        }

        public function render_form($field){
            $file   = __DIR__.'/tpl/form.php';
            if(file_exists($file)) {
                require __DIR__ . '/tpl/form.php';
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
            $field['s_type']   = isset($field['s_type'])?$field['s_type']:$field['type'];

//            // find key (not actual field key, more the html attr name)
//            $field['field_key'] = str_replace("fields[", "", $options['field_key']);
//            $field['field_key'] = str_replace("][type]", "", $options['field_key']) ;

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