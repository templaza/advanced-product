<?php

namespace Advanced_Product;

defined('ADVANCED_PRODUCT') or exit();

abstract class Base{
    protected $core;
    protected $theme;
    protected $post_type;

    protected $cache    = array();

    public function __construct($core = null, $post_type = null)
    {
        $this -> core           = $core;
        $this -> theme          = \wp_get_theme();
        $this -> post_type      = $post_type;

        $this -> hooks();
    }

    public function hooks(){}

    public function get_name(){
        $class_name = get_called_class();
//        $meta_name  = preg_replace('#^(.*?[\\\\])+#i', '', $class_name);

        $reflector  = new \ReflectionClass($class_name);
        $meta_name  = $reflector -> getShortName();

        return strtolower($meta_name);
    }

    public function get_property($name, $default = ''){
        if(isset($this -> {$name})){
            return $this -> {$name};
        }
        return $default;
    }

    protected function _get_store_id($args = array()){
        $_args      = \func_get_args();
        $store_id   = serialize($_args);

        return md5($store_id);
    }
}