<?php

namespace Advanced_Product\Field\Layout;

use Advanced_Product\AP_Functions;
use Advanced_Product\Base;
use Advanced_Product\Field_Layout;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\Field\Layout\Range_Slider')){
    class Range_Slider extends Field_Layout{

        protected $field    = array();
        protected $group    = array();

        public function hooks(){
            parent::hooks();

            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        public function enqueue_scripts(){
            wp_enqueue_style('advanced-product__fields-layout-range-slider', AP_Functions::get_my_url()
                . '/core/field-layouts/range_slider/range_slider.css');
            wp_enqueue_script('advanced-product__fields-layout-range-slider', AP_Functions::get_my_url()
                    . '/core/field-layouts/range_slider/range_slider.js',
                array('jquery', 'jquery-ui-core', 'jquery-ui-slider'));
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
            $field['s_type']        = isset($field['s_type'])?$field['s_type']:$field['type'];
            $field['s_range_step']    = isset($field['s_range_step'])?$field['s_range_step']:1;
            $field['s_range_to']    = isset($field['s_range_to'])?$field['s_range_to']:'';
            $field['s_range_from']  = isset($field['s_range_from'])?$field['s_range_from']:'';

            $key = $field['name'];

            $file   = __DIR__.'/tpl/search_settings.php';
            if(file_exists($file)) {
                require $file;
            }

        }
    }
}

new Range_Slider();