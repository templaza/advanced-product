<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Helper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

?>
<?php
$msrp           = get_field('ap_price_msrp', get_the_ID());
$price          = get_field('ap_price', get_the_ID());
$rental         = get_field('ap_rental_price', get_the_ID());
$rental_unit    = get_field('ap_rental_unit', get_the_ID());
$product_type   = get_field('ap_product_type', get_the_ID());

if ((!$product_type || in_array('sale', $product_type)) && !empty($price)) {

    $html = '<p class="uk-background-primary uk-padding-small uk-light ap-pricing">';
    $html .= sprintf('<span class="ap-price uk-h3"><b> %s</b> %s </span>',
        esc_html__(' ', 'advanced-product'), AP_Helper::format_price($price));
    if (!empty($msrp)) {
        $html .= sprintf('<span class="ap-price-msrp"> %s  %s </span>',
            esc_html__('MSRP:', 'advanced-product'), AP_Helper::format_price($msrp));
    }
    $html .= '</p>';

    echo balanceTags($html);
} ?>
<?php if (!empty($product_type) && in_array('rental', $product_type) && !empty($rental)) { ?>
    <p class="uk-background-primary uk-padding-small uk-light ap-pricing">
        <label class="single-price-label"><?php esc_html_e('RENTAL PRICE:','templaza-framework');?></label>
        <span class="ap-price uk-h3 ap-rental-price">
            <?php echo esc_html(AP_Helper::format_price($rental)); ?>
            <?php if(!empty($rental_unit)){?>
                <span class="rental-unit uk-text-meta"><?php echo ' / '.esc_html($rental_unit);?></span>
            <?php } ?>
        </span>
    </p>
<?php } ?>
