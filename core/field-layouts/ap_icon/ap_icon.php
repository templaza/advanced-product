<?php

namespace Advanced_Product\Field\Layout;

use Advanced_Product\AP_Functions;
use Advanced_Product\Field_Layout;

defined('ADVANCED_PRODUCT') or exit();

if(!class_exists('Advanced_Product\Field\Layout\AP_Icon')) {
    class AP_Icon extends Field_Layout{

        var $name,
            $title,
            $category,
            $defaults,
            $l10n, $enqueue_required = false;

        public function __construct($field = array(), $group = array())
        {

            $this->name = $this -> get_name();
            $this->label = __('AP Icon', 'advanced-product');
            $this->category = __('Content','advanced-product');
            $this->defaults = array(
                'preview_size'	=>	'thumbnail',
                'library'		=>	'all'
            );
            $this->l10n = array(
                'select'		=>	__('Add Image to Gallery','acf'),
                'edit'			=>	__("Edit Image",'acf'),
                'update'		=>	__("Update Image",'acf'),
                'uploadedTo'	=>	__("uploaded to this post",'acf'),
                'count_0'		=>	__("No images selected",'acf'),
                'count_1'		=>	__("1 image selected",'acf'),
                'count_2'		=>	__("%d images selected",'acf'),
            );

            parent::__construct($field, $group);
        }

        public function hooks()
        {
            parent::hooks();

            // register field
            add_filter('acf/registered_fields', array($this, 'registered_fields'), 10, 1);

            // field
//            add_filter('acf/load_field/type=' . $this->name, array($this, 'load_field'), 10, 3);
//            add_filter('acf/update_field/type=' . $this->name, array($this, 'update_field'), 10, 2);
            add_action('acf/create_field/type=' . $this->name, array($this, 'create_field'), 10, 1);

            // actions
            add_action('acf/input/admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 0);


            add_action('admin_footer', array($this,'admin_footer'));
        }

        public function admin_footer(){
            $file   = __DIR__.'/tpl/ap_icon.tpl.php';
            if(file_exists($file)) {
                require_once $file;
            }
            $file   = __DIR__.'/tpl/ap_icon.modal.tpl.php';
            if(file_exists($file)) {
                require_once $file;
            }

        }

        function registered_fields( $fields )
        {
            // defaults
            if( !$this->category )
            {
                $this->category = __('Basic', 'acf');
            }


            // add to array
            $fields[ $this->category ][ $this->name ] = $this->label;


            // return array
            return $fields;
        }


        function create_field( $field )
        {
            $file   = __DIR__.'/tpl/default.php';
            if(file_exists($file)) {
                require $file;
            }
        }

        public function admin_enqueue_scripts(){
            wp_enqueue_style(ADVANCED_PRODUCT.'__css-uikit', AP_Functions::get_my_url()
                .'/assets/vendor/uikit/css/uikit.min.css', array(), '3.15.10');

            $ap_icon_options    = array('advanced-product');
            if(defined('TEMPLAZA_FRAMEWORK')){
                $ap_icon_options[]  = TEMPLAZA_FRAMEWORK.'_uikit_js';
            }else{
                wp_enqueue_script('advanced-product__js_uikit');
                $ap_icon_options[]  = 'advanced-product__js-uikit';
            }

            wp_enqueue_script(ADVANCED_PRODUCT.'__field-ap_icon', AP_Functions::get_my_url()
                .'/core/field-layouts/ap_icon/ap_icon.js', $ap_icon_options);

            if(!$this -> enqueue_required) {
                wp_localize_script(ADVANCED_PRODUCT . '__field-ap_icon', 'APIconFieldConfig',
                    array(
                        'icons' => $this->_get_tabs(),
                        'i10n'  => array(
                            'no_icon'   => __('No Icon', 'advanced-product')
                        )
                    ));
            }
            $this -> enqueue_required   = true;
        }

        protected function _get_tabs(){

            $store_id   = $this -> _get_store_id(__METHOD__);

            if(isset($this -> cache[$store_id])){
                return $this -> cache[$store_id];
            }

            $fawSolidIcons      = file_get_contents(ADVANCED_PRODUCT_PATH.'/assets/lib/font-awesome/solid.json');
            $fawSolidIcons      = json_decode($fawSolidIcons);
            $fawBrandsIcons     = file_get_contents(ADVANCED_PRODUCT_PATH.'/assets/lib/font-awesome/brands.json');
            $fawBrandsIcons     = json_decode($fawBrandsIcons);
            $fawRegularIcons    = file_get_contents(ADVANCED_PRODUCT_PATH.'/assets/lib/font-awesome/regular.json');
            $fawRegularIcons    = json_decode($fawRegularIcons);
            $uiKitIcons         = file_get_contents(ADVANCED_PRODUCT_PATH.'/assets/lib/uikit/uikit.json');
            $uiKitIcons         = json_decode($uiKitIcons, true);

            foreach($uiKitIcons as $key => &$uikitIcon){
                $uikitIcon  = array(
                    'displayPrefix' => 'data-uk-icon',
                    'filter'        => $key,
                    'name'          => ucfirst(str_replace(array('-','_'), ' ', $key)),
                    'selector'      => $key,
                    'prefix'        => '',
//                    'path'          => $uikitIcon
                );
            }

            $initial_tabs   = array(
                'ap_all'   => array(
                    'label' => __('All Icons', 'advanced-product'),
                    'labelIcon'=> 'apicon-filter',
                    'name' =>  'ap_all',
                    'native' => true
                ),
                'uikit-icon' => array(
                    'displayPrefix'=> 'data-uk-icon',
//                        'enqueue'=> ['http://wp2021.templaza.net/duongtv/wordpress_plugi…r/assets/lib/font-awesome/css/fontawesome.min.css'],
//                        'fetchJson'=> 'http://wp2021.templaza.net/duongtv/wordpress_plugin/wp-content/plugins/elementor/assets/lib/font-awesome/js/regular.js',
//                        'fetchJson'=> $fetchfawRegular,
                    'icons'     => $uiKitIcons,
                    'label'     => __('UIKit', 'advanced-product'),
                    'labelIcon' => 'fab fa-uikit',
                    'name'      => 'uikit-icon',
                    'native'    => true,
                    'prefix'    => '',
//                        'url'=> 'http://wp2021.templaza.net/duongtv/wordpress_plugin/wp-content/plugins/elementor/assets/lib/font-awesome/css/regular.min.css',
                    'ver'       => '3.15.6',
                ),
                'fa-regular' => array(
                    'displayPrefix'=> 'far',
//                        'enqueue'=> ['http://wp2021.templaza.net/duongtv/wordpress_plugi…r/assets/lib/font-awesome/css/fontawesome.min.css'],
//                        'fetchJson'=> 'http://wp2021.templaza.net/duongtv/wordpress_plugin/wp-content/plugins/elementor/assets/lib/font-awesome/js/regular.js',
//                        'fetchJson'=> $fetchfawRegular,
                    'icons'=> $this -> _prepare_icons_source($fawRegularIcons),
                    'label'=> __('Font Awesome - Regular', 'advanced-product'),
                    'labelIcon'=> 'fab fa-font-awesome-alt',
                    'name'=> 'fa-regular',
                    'native'=> true,
                    'prefix'=> 'fa-',
//                        'url'=> 'http://wp2021.templaza.net/duongtv/wordpress_plugin/wp-content/plugins/elementor/assets/lib/font-awesome/css/regular.min.css',
                    'ver'=> '5.15.4',
                ),
                'fa-solid' => array(
                    'displayPrefix'=> 'fas',
//                        'enqueue'=> ['http://wp2021.templaza.net/duongtv/wordpress_plugi…r/assets/lib/font-awesome/css/fontawesome.min.css'],
//                        'fetchJson'=> 'http://wp2021.templaza.net/duongtv/wordpress_plugin/wp-content/plugins/elementor/assets/lib/font-awesome/js/regular.js',
//                        'fetchJson'=> $fetchfawRegular,
                    'icons'=> $this -> _prepare_icons_source($fawSolidIcons),
                    'label'=> __('Font Awesome - Solid', 'advanced-product'),
                    'labelIcon'=> 'fab fa-font-awesome',
                    'name'=> 'fa-solid',
                    'native'=> true,
                    'prefix'=> 'fa-',
//                        'url'=> 'http://wp2021.templaza.net/duongtv/wordpress_plugin/wp-content/plugins/elementor/assets/lib/font-awesome/css/regular.min.css',
                    'ver'=> '5.15.4',
                ),
                'fa-brands' => array(
                    'displayPrefix'=> 'fab',
//                        'enqueue'=> ['http://wp2021.templaza.net/duongtv/wordpress_plugi…r/assets/lib/font-awesome/css/fontawesome.min.css'],
//                        'fetchJson'=> 'http://wp2021.templaza.net/duongtv/wordpress_plugin/wp-content/plugins/elementor/assets/lib/font-awesome/js/regular.js',
//                        'fetchJson'=> $fetchfawRegular,
                    'icons'=> $this -> _prepare_icons_source($fawBrandsIcons),
                    'label'=> __('Font Awesome - Brands', 'advanced-product'),
                    'labelIcon'=> 'fab fa-font-awesome-flag',
                    'name'=> 'fa-brands',
                    'native'=> true,
                    'prefix'=> 'fa-',
//                        'url'=> 'http://wp2021.templaza.net/duongtv/wordpress_plugin/wp-content/plugins/elementor/assets/lib/font-awesome/css/regular.min.css',
                    'ver'=> '5.15.4',
                ),
            );
            $initial_tabs = apply_filters( 'advanced-product/field/ap_icon/native', $initial_tabs );

            if(!empty($initial_tabs)){
                $this -> cache[$store_id]   = $initial_tabs;
            }

            return $initial_tabs;
        }


        protected function _prepare_icons_source($source, $source_type = 'icomoon', $font_type = false){

            $store_id   = $this -> _get_store_id(__METHOD__, $source, $source_type, $font_type);

            if(isset($this -> cache[$store_id])){
                return $this -> cache[$store_id];
            }

            $icons  = array();

            if(!$source){
                return $icons;
            }

            $prefix         = '';
            $sourceIcons    = array();
            $displayPrefix  = '';
            switch($source_type){
                case 'icomoon':
                    $preferences    = $source -> preferences;
                    $prefix         = $preferences -> fontPref -> prefix;
                    $sourceIcons    = $source -> icons;
                    $displayPrefix  = $preferences -> fontPref -> postfix;
                    break;
                case 'fontello':
                    $prefix         = $source -> css_prefix_text;
                    $sourceIcons    = $source -> glyphs;
                    $displayPrefix  = isset($source -> css_use_suffix) && $source -> css_use_suffix?$source -> css_use_suffix:$displayPrefix;
                    break;
            }

            if(!count($sourceIcons)){
                return $icons;
            }

            foreach($sourceIcons as $item) {
                $icon_name  = '';
                switch($source_type){
                    default:
                    case 'icomoon':
                        $icon_name  = $item -> properties -> name;
                        break;
                    case 'fontello':
                        $icon_name  = $item -> css;
                        break;
                }

                $icon_title = ucfirst(str_replace(array('-','_'), ' ', $icon_name));

                $icons[$icon_name]    = array(
                    'displayPrefix' => $displayPrefix,
                    'filter'        => $icon_name,
                    'name'          => $icon_title,
                    'prefix'        => $prefix,
                    'selector'      => $prefix.$icon_name,
                );
            }

            return $this -> cache[$store_id]    = $icons;
        }
    }
}

new AP_Icon();