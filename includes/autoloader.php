<?php

defined('ABSPATH') or exit();

require_once __DIR__.'/defines.php';
require_once __DIR__.'/ap-functions.php';
//if(!is_admin()){
require_once __DIR__.'/ap-templates.php';
//}


////var_dump(class_exists('acf_taxonomy_field_walker'));
//if(!class_exists('acf_taxonomy_field_walker')){
//    if(file_exists(ADVANCED_PRODUCT_LIBRARY_PATH.'/acf/core/fields/taxonomy.php')) {
////        var_dump(file_exists(ADVANCED_PRODUCT_LIBRARY_PATH . '/acf/core/fields/taxonomy.php'));
////        var_dump(ADVANCED_PRODUCT_LIBRARY_PATH . '/acf/core/fields/taxonomy.php');
//        require_once ADVANCED_PRODUCT_LIBRARY_PATH.'/acf/core/fields/taxonomy.php';
//    }
//}
////var_dump(class_exists('acf_taxonomy_field_walker'));
////die(__FILE__);



//if ( ! class_exists( 'Acf' ) && ! defined ( 'ACF_LITE' ) ) {
//    define( 'ACF_LITE' , true );
//
//    // Include Advanced Custom Fields
//    include_once( ADVANCED_PRODUCT_LIBRARY_PATH . '/acf/acf.php' );
//}
//
//if ( ! class_exists( 'acf_options_page_plugin' ) ) {
//    include( 'library/acf-options-page/acf-options-page.php' );
//}
//if ( ! function_exists( 'acf_register_flexible_content_field' ) ) {
//    include( 'library/acf-flexible-content/acf-flexible-content.php' );
//}
//if ( ! function_exists( 'acf_register_fields' ) ) {
//    include( 'library/acf-gallery/acf-gallery.php' );
//}

//var_dump(wp_script_is('acf-field-group', 'enqueued'));
//var_dump(acf());
//var_dump(class_exists('acf_field_group'));
//die(__FILE__);

//require_once __DIR__.'/acf-functions.php';
require_once __DIR__ . '/helpers/basehelper.php';
require_once __DIR__ . '/helpers/ap-helper.php';
require_once __DIR__ . '/helpers/ap-product-helper.php';
require_once __DIR__ . '/helpers/fieldhelper.php';
require_once __DIR__ . '/helpers/ap-custom-field-helper.php';
require_once __DIR__ . '/helpers/taxonomyhelper.php';
require_once __DIR__ . '/helpers/ap-custom-taxonomy-helper.php';
require_once __DIR__ . '/classes/class-base.php';
require_once __DIR__ . '/classes/class-field_layout.php';
require_once __DIR__ . '/classes/class-acf_taxonomy.php';
//require_once __DIR__ . '/classes/class-acf_taxonomy_walker.php';
require_once __DIR__ . '/classes/class-post_type.php';
require_once __DIR__ . '/classes/class-taxonomy.php';
require_once __DIR__ . '/classes/class-meta_box.php';
require_once __DIR__ . '/classes/class-custom_taxonomy.php';
require_once __DIR__ . '/classes/class-shortcodeAP.php';
//require_once __DIR__.'/classes/class-meta_box.php';



