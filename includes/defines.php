<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!defined('ADVANCED_PRODUCT_PATH')){
    define('ADVANCED_PRODUCT_PATH', dirname( dirname(__FILE__)));
}
if(!defined('ADVANCED_PRODUCT')){
    define('ADVANCED_PRODUCT', basename(ADVANCED_PRODUCT_PATH));
}
if(!defined('ADVANCED_PRODUCT_CORE_PATH')){
    define('ADVANCED_PRODUCT_CORE_PATH', ADVANCED_PRODUCT_PATH.'/core');
}
if(!defined('ADVANCED_PRODUCT_PAGES_PATH')){
    define('ADVANCED_PRODUCT_PAGES_PATH', ADVANCED_PRODUCT_CORE_PATH.'/pages');
}
if(!defined('ADVANCED_PRODUCT_TAXONOMIES_PATH')){
    define('ADVANCED_PRODUCT_TAXONOMIES_PATH', ADVANCED_PRODUCT_CORE_PATH.'/taxonomies');
}
if(!defined('ADVANCED_PRODUCT_POST_TYPES_PATH')){
    define('ADVANCED_PRODUCT_POST_TYPES_PATH', ADVANCED_PRODUCT_CORE_PATH.'/post-types');
}
if(!defined('ADVANCED_PRODUCT_PLUGIN_DIR_PATH')){
    define('ADVANCED_PRODUCT_PLUGIN_DIR_PATH', dirname(ADVANCED_PRODUCT_PATH ));
}
if(!defined('ADVANCED_PRODUCT_LIBRARY_PATH')){
    define('ADVANCED_PRODUCT_LIBRARY_PATH', __DIR__.'/library');
}
if(!defined('ADVANCED_PRODUCT_CLASSES_PATH')){
    define('ADVANCED_PRODUCT_CLASSES_PATH', __DIR__.'/classes');
}
if(!defined('ADVANCED_PRODUCT_TEMPLATE_PATH')){
    define('ADVANCED_PRODUCT_TEMPLATE_PATH', ADVANCED_PRODUCT_PATH.'/templates');
}
if(!defined('ADVANCED_PRODUCT_FIELD_LAYOUT_PATH')){
    define('ADVANCED_PRODUCT_FIELD_LAYOUT_PATH', ADVANCED_PRODUCT_CORE_PATH.'/field-layouts');
}

if(!defined('ADVANCED_PRODUCT_THEME_PATH')){
    define('ADVANCED_PRODUCT_THEME_PATH', get_stylesheet_directory().'/'.ADVANCED_PRODUCT);
}
if(!defined('ADVANCED_PRODUCT_THEME_TEMPLATE_PATH')){
    define('ADVANCED_PRODUCT_THEME_TEMPLATE_PATH', ADVANCED_PRODUCT_THEME_PATH.'/templates');
}
if(!defined('ADVANCED_PRODUCT_TEMPLAZA_FRAMEWORK_TEMPLATE_PATH')){
    define('ADVANCED_PRODUCT_TEMPLAZA_FRAMEWORK_TEMPLATE_PATH', plugin_dir_path(ADVANCED_PRODUCT_PATH)
        .'templaza-framework/templates/'.ADVANCED_PRODUCT);
}

if(!defined('ADVANCED_PRODUCT_THEME_TEMPLATE_SHORTCODE_PATH')){
    define('ADVANCED_PRODUCT_THEME_TEMPLATE_SHORTCODE_PATH', ADVANCED_PRODUCT_THEME_PATH.'/templates/shortcodes');
}
if(!defined('ADVANCED_PRODUCT_TEMPLAZA_FRAMEWORK_TEMPLATE_SHORTCODE_PATH')){
    define('ADVANCED_PRODUCT_TEMPLAZA_FRAMEWORK_TEMPLATE_SHORTCODE_PATH', plugin_dir_path(ADVANCED_PRODUCT_PATH)
        .'templaza-framework/templates/'.ADVANCED_PRODUCT.'/shortcodes');
}

//if(!defined('ADVANCED_PRODUCT_PLUGIN_URL')){
//    define( 'ADVANCED_PRODUCT_PLUGIN_URL', untrailingslashit( plugins_url( ADVANCED_PRODUCT, ADVANCED_PRODUCT.'.php'  ) ) );
//}