<?php

namespace Advanced_Product\Helper;

defined('ADVANCED_PRODUCT') or exit();

class BaseHelper{
    protected static $cache    = array();

    protected static function _get_store_id($args = array()){
        $_args      = \func_get_args();
        $store_id   = serialize($_args);

        return md5($store_id);
    }
}