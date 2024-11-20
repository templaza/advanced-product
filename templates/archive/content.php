<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Templates;

/*
 * Get uk-options
 * This hook used in uiadvancedproducts widget of elementor
 * */
$uk_options    = apply_filters('advanced-product/archive/uk-options', array());
$ap_columns    = get_option('advanced_product_catalog_columns',3);
$ap_columns_gap    = get_option('advanced_product_catalog_columns_gap','');
?>
<div class="templaza-ap-archive uk-grid-<?php echo esc_attr($ap_columns_gap);?> ap-list uk-child-width-1-<?php echo esc_attr($ap_columns);?>@m uk-child-width-1-2@s uk-child-width-1-1" data-uk-grid>
    <?php
    while (have_posts()): the_post();
        AP_Templates::load_my_layout('archive.content-item');
    endwhile;
    ?>
</div>
