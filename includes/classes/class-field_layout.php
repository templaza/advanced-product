<?php
namespace Advanced_Product;

use Advanced_Product\Helper\FieldHelper;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\Field_Layout')) {
    class Field_Layout extends Base
    {

        public function __construct($field = array(), $group = array())
        {
            parent::__construct(null, null);

            $this -> field  = $field;
            $this -> group  = $group;

//            $this -> hooks();

        }
        public function hooks(){

            parent::hooks();

            // Register search options hook
            if(method_exists($this, 'render_search_settings')) {
                add_action('advanced-product/field/create_field_search_options/type='.$this -> get_name(), array($this, 'render_search_settings'));
            }

            // Register search options hook
            if(method_exists($this, 'render_form')) {
                add_action('advanced-product/field/create_form/type='.$this -> get_name(), array($this, 'render_form'));
            }

            // Enqueue
            if(method_exists($this, 'admin_enqueue_scripts')) {
                add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            }
        }
    }
}