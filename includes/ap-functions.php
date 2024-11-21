<?php

namespace Advanced_Product;

defined( 'ADVANCED_PRODUCT' ) || exit;

if(!class_exists('Advanced_Product\AP_Functions')){

    class AP_Functions{
        protected static $cache         = array();
        protected static $shortcode = '';

        public static function get_my_data(){
            $storeId    = md5(__METHOD__);

            if(isset(self::$cache[$storeId])){
                return self::$cache[$storeId];
            }

            $file   = ADVANCED_PRODUCT_PATH.'/'.ADVANCED_PRODUCT.'.php';

            if(!file_exists($file)){
                return false;
            }
            if( !function_exists('get_plugin_data') ){
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }

            if($plugin = \get_plugin_data( $file, true, true )){

                $other_data = get_file_data( $file,
                    array(
                        'Forum' => 'Forum',
                        'Ticket' => 'Ticket',
                        'FanPage' => 'FanPage',
                        'Twitter' => 'Twitter',
                        'Google' => 'Google+'
                    ),
                    'plugin' );
                $plugin = array_merge($plugin, $other_data);

                self::$cache[$storeId]  = $plugin;
                return $plugin;
            }
            return false;
        }

        public static function get_my_url(){
            return untrailingslashit(plugins_url().'/'.ADVANCED_PRODUCT);
        }
        public static function get_my_frame_url(){
            return plugins_url().'/'.ADVANCED_PRODUCT.'/framework';
        }

        public static function get_my_version(){
            $plugin = self::get_my_data();

            return $plugin['Version'];
        }

        public static function get_my_text_domain(){
            $plugin = self::get_my_data();

            $text_domain    = ($plugin && isset($plugin['TextDomain']))?$plugin['TextDomain']:'advanced-product';
            return $text_domain;
        }

        public static function get_my_theme_css_uri(){
            return get_template_directory_uri().'/'.ADVANCED_PRODUCT.'/css';
        }

        public static function get_attribute_value($key='attribute', $attrib_key = ''){
            $attributes = self::get_attributes($key);

            if(isset($attributes[$attrib_key])){
                return $attributes[$attrib_key];
            }

            return false;
        }
        public static function get_attributes($key='attribute'){
            $store_id   = __CLASS__;
            $store_id  .= ':'.$key;
            $store_id   = md5($store_id);
            if(isset(self::$cache[$store_id])){
                return self::$cache[$store_id];
            }
            return false;
        }
        public static function add_attributes($key='attribute', $attributes = array()){
            $store_id   = __CLASS__;
            $store_id  .= ':'.$key;
            $store_id   = md5($store_id);

            self::$cache[$store_id] = $attributes;
        }

        public static function merge_array($source, $destination, $recursive = true,  $allowNull = false){
            return Array_Helper::merge($source, $destination, $recursive, $allowNull);
        }

        public static function get_framework_logo_url(){
            $logo_url   = self::get_my_url();
            $log_path   = AMI_CODE_MANAGER.'/assets/images/logo.svg';
            if(file_exists($log_path)){
                return $logo_url.'/assets/images/logo.svg';
            }
            return '';
        }

        /**
         * Get theme's default logo when option has not set in config
         * Note: your logo file should have in your theme base folder. Ex: your-theme/assets/images
         * @param string $file_name
         * @param array|string $files_ext
         * @param string $base_folder
         * @return string
         * */
        public static function get_theme_default_logo_url($file_name, $files_ext = array('.svg', '.png'), $base_folder = 'assets/images'){

            if(empty($file_name) || empty($files_ext) || empty($base_folder)){
                return '';
            }

            $logo_url = $logo_path = '/'.$base_folder.'/'.$file_name;
            $logo_url     = get_template_directory_uri().$logo_url;
            $logo_path    = get_template_directory().$logo_path;

            if(is_array($files_ext)){
                foreach($files_ext as $ext){
                    if(file_exists($logo_path.$ext)){
                        return $logo_url.$ext;
                    }
                }
            }elseif(is_string($files_ext) && $logo_path.$files_ext){
                return $logo_url.$files_ext;
            }
            return '';
        }

        /**
         * Check url is external
         * @param string $url
         * @return bool true|false
         * */
        public static function is_external_url($url){
            if(!$url){
                return false;
            }

            $url_host       = parse_url($url, PHP_URL_HOST);
            $internal_host  = parse_url(get_site_url(), PHP_URL_HOST);

            if($url_host != $internal_host){
                return true;
            }
            return false;
        }

        /**
         * Check extension of a file
         * @param string $file
         * @param string $ext_check The extension of file to check
         * @return bool true|false|null
         * */
        public static function file_ext_exists($file, $ext_check){
            if(!$file || !$ext_check){
                return null;
            }

            $file_type  = wp_check_filetype($file);
            if(!$file_type['ext']){
                return null;
            }
            if(is_array($ext_check) && in_array($file_type['ext'], $ext_check)){
                return true;
            }
            return (is_string($ext_check) && $file_type['ext'] == $ext_check);
        }
        /**
         * Translate group field option
         * */
        public static function get_translated_term($term_id, $taxonomy, $language) {

            $translated_term_id = icl_object_id($term_id, $taxonomy, true, $language);

            $translated_term_object = get_term_by('id', $translated_term_id, $taxonomy);

            return $translated_term_object->name;
        }

    }
}