<?php
namespace Advanced_Product;

use Advanced_Product\Helper\FieldHelper;
use ScssPhp\ScssPhp\ValueConverter;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\Field_Layout')) {
    class Field_Layout extends Base
    {

        protected $field;
        protected $group;

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

            // Before render form
//            if(method_exists($this, 'render_form')) {
//                add_action('advanced-product/field/create_form/type='.$this -> get_name(), array($this, 'before_render_form'));
//            }

            // Register search options hook
//            if(method_exists($this, 'render_form')) {
                add_action('advanced-product/field/create_form/type='.$this -> get_name(), array($this, 'render_form'));
//            }

            // Enqueue
            if(method_exists($this, 'admin_enqueue_scripts')) {
                add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            }

//            add_filter('advanced-product/field/search_form/load_value/type='.$this -> get_name(),
//                array($this, 'search_form_load_value'), 10 , 3);
            add_filter('advanced-product/field/search-form/prepare_field/type='.$this -> get_name(),
                array($this, 'prepare_field'));
        }

        /**
         * Get search form html to display on front-end
         * @param array $field An optional of field
         * @return string Html of search form
         * */
        public function render_form($field){
            $reflector      = new \ReflectionClass(get_called_class());
            $base_path      = $reflector -> getFileName();
            $base_path      = dirname($base_path);
            $folder_name    = basename($base_path);
            $base_file      = $base_path.'/tpl/form.php';
            $theme_file     = ADVANCED_PRODUCT_THEME_PATH.'/field-layouts/'.$folder_name.'/form.php';

            if(file_exists($theme_file) || file_exists($base_file)) {
                // Before render form
                $field = apply_filters('advanced-product/field/search-form/prepare_field/type=' . $this->get_name(), $field);
            }

            if(file_exists($theme_file)){
                require $theme_file;
            }else{
                if(file_exists($base_file)) {
                    require $base_file;
                }
            }

        }

        /**
         * Prepare field of search form
         * @param array $field An optional of search field
         * @return int|string|array|boolean New value of search field
         * */
        public function prepare_field($field){

            $query_var  = \get_query_var('field');
            $field['default_value']  = isset($field['s_default_value'])?$field['s_default_value']:$field['default_value'];
            $field['value']  = isset($field['s_default_value'])?$field['s_default_value']:$field['default_value'];
            $field['value']  = apply_filters('acf/load_value/type='.$field['type'] ,
                ((isset($field['value']))?$field['value']:$field['default_value']),
                $field['field_group'], $field );

            if(isset($field['_name']) && isset($query_var[$field['_name']])){
                $field['value']  = $query_var[$field['_name']];
            }

            return $field;
        }

        /**
         * Override value when filter of search form
         * @param int|string|array|boolean $value An optional value of search field
         * @param int $group An optional id of field
         * @param array $field An optional of search field
         * @return int|string|array|boolean New value of search field
         * */
        public function search_form_load_value($value, $group, $field){

            $query_var  = \get_query_var('field');
            if(isset($field['_name']) && isset($query_var[$field['_name']])){
                $value  = $query_var[$field['_name']];
            }

            remove_filter('advanced-product/field/search_form/load_value/type='.$this -> get_name(),
                array($this, 'search_form_load_value'));

            return $value;
        }

        /**
         * Get html path by layout
         * @param string $layout Layout name (.php file) in tpl folder
         * */
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
}