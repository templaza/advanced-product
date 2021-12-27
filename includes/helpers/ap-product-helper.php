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

}