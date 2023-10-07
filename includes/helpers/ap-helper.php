<?php

namespace Advanced_Product\Helper;

use Advanced_Product\AP_Functions;

defined('ADVANCED_PRODUCT') or exit();

class AP_Helper extends BaseHelper {
    protected static $cache    = array();

    /*
     * Format Price
     */
    public static function format_price($price = '0')
    {
        $store_id   = static::_get_store_id($price);

        if(isset(static::$cache[$store_id])){
            return static::$cache[$store_id];
        }

        $original_price = $price;
        $symbol         = get_option('options_ap_currency_symbol', '$');
        $placement      = get_option('options_ap_symbol_placement', 'prepend');
        $decimals       = get_option('options_ap_price_num_decimals', 0);
        $decimal_sep    = get_option('options_ap_price_decimal_sep', ',');
        $thousands_sep  = get_option('options_ap_price_thousands_sep', ',');

        if ('space' == $thousands_sep) {
            $thousands_sep = ' ';
        }
        $price = number_format($price, $decimals, $decimal_sep, $thousands_sep);

        if ('append' == $placement) {
            $price = $price . '&nbsp;' . $symbol;
        } else {
            $price = $symbol . '' . $price;
        }
        return apply_filters('advanced-product/format_price', $price, $original_price);
    }

    /**
     * Retrieve page ids - used for inventory. returns -1 if no page is found.
     *
     * @param string $page Page slug.
     * @return int
     */
    public static function get_page_id($page){
        $page_obj   = get_field('ap_'.$page.'_page_id', 'option');
        $page_id    = -1;
        if(($page_obj instanceof \WP_Post) && !is_wp_error($page_obj)){
            $page_id    = $page_obj -> ID;
        }elseif(is_numeric($page_obj)){
            $page_id    = $page_obj;
        }
        $page_id = apply_filters( 'advanced-product__get_' . $page . '_page_id', $page_id );

        return $page_id ? absint( $page_id ) : -1;
    }
}