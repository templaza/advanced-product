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

}