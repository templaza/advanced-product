<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Templates;

extract($args);

if(isset($products) && !empty($products) && $products -> have_posts()){
    do_action('advanced-product/shortcodes/advanced-product/compare/before_content');
?>
<div class="uk-container uk-container-large uk-margin-top" data-uk-slider>
    <ul class="ap-product-compare-items uk-slider-items uk-child-width-1-2@s uk-child-width-1-3@m uk-grid">
        <?php while($products -> have_posts()){ ?>
            <li class="ap-product-compare-item uk-transition-toggle">
                <?php $products -> the_post();
                AP_Templates::load_my_layout('archive.content-item');
                ?>
            </li>
            <?php }?>
    </ul>

    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-previous data-uk-slider-item="previous"></a>
    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" data-uk-slidenav-next data-uk-slider-item="next"></a>
</div>
<?php
    do_action('advanced-product/shortcodes/advanced-product/compare/after_content');
} ?>
