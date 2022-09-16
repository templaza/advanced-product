<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Templates;

/*
 * Get uk-options
 * This hook used in uiadvancedproducts widget of elementor
 * */
$uk_options    = apply_filters('advanced-product/archive/uk-options', array());
?>
<div class="templaza-ap-archive ap-list uk-child-width-1-2" data-uk-grid>
    <?php
    while (have_posts()): the_post();
        AP_Templates::load_my_layout('archive.content-item');
    endwhile;
    ?>
</div>
