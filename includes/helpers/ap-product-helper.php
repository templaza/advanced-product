<?php

namespace Advanced_Product\Helper;

use Advanced_Product\AP_Functions;

defined('ADVANCED_PRODUCT') or exit();

class AP_Product_Helper extends BaseHelper {
    protected static $cache    = array();

    protected static $fields    = array();

    public static function setField($field){
        static::$fields[]   = $field;
    }
    public static function getFields(){
        return static::$fields;
    }

    public static function get_products($args = array()){

        $store_id   = __METHOD__;
        $store_id  .= '::'.serialize($args);
        $store_id   = md5($store_id);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $user   = wp_get_current_user();

        // First lets set some arguments for the query:
        // Optionally, those could of course go directly into the query,
        // especially, if you have no others but post type.
        $default_args   = array(
            'post_type' => 'ap_product',
            'author'        =>  $user->ID,
            'posts_per_page' => 5,
            // Several more arguments could go here. Last one without a comma.
        );
        $args = array_merge($default_args, $args);

        // Query the posts:
        $query = new \WP_Query($args);

        if(!empty($query) && !is_wp_error($query)){
            return static::$cache[$store_id]    = $query;
        }

        return false;

    }

    public static function get_compare_product_ids_list(){

        $cookie_key     = 'advanced-product__compare-list';
        $compare_list   = isset($_COOKIE[$cookie_key])?$_COOKIE[$cookie_key]:false;

        if(!$compare_list){
            return array();
        }

        return explode('|', $compare_list);
    }

    public static function get_compare_products(){
        $pids   = static::get_compare_product_ids_list();

        if(!count($pids)){
            return false;
        }

        return $products   = static::get_products(array(
            'post__in' => $pids));
    }

    public static function get_compare_product_count(){
        $products   = static::get_compare_products();

        if(!$products){
            return 0;
        }

        return $products -> found_posts;
    }
}