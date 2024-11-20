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
$rental_value    = get_field('ap_rental_unit', get_the_ID());
if($rental_value){
    $field_rental = get_field_object('ap_rental_unit');
    $rental_unit = $field_rental['choices'][ $rental_value ];
}
$product_type   = get_field('ap_product_type', get_the_ID());
$price_sold     = get_field('ap_price_sold', get_the_ID());
$price_contact  = get_field('ap_price_contact', get_the_ID());

$f_value            = get_field('unit-price', get_the_ID());
$call2buy_value     = get_field('call-to-buy', get_the_ID());
$price_notice_value = get_field('price-notice', get_the_ID());

if($product_type == 'sale'){
    $product_type = array('sale');
}
if($price_sold == ''){
    $price_sold = __('Sold','advanced-product');
}
if($price_contact == ''){
    $price_contact = __('Contact','advanced-product');
}

if ((!$product_type || in_array('sale', $product_type)) && !empty($price)) {

    $html = '<p class="uk-background-primary uk-padding-small uk-light ap-pricing">';
    $html .= sprintf('<span class="ap-price uk-h3"><b> %s</b> %s </span>',
        esc_html__(' ', 'advanced-product'), AP_Helper::format_price($price));
    if (!empty($msrp)/* && $show_price_msrp*/) {
        $html .= sprintf('<span class="ap-price-msrp"> %s  %s </span>',
            esc_html__('MSRP:', 'advanced-product'), AP_Helper::format_price($msrp));
    }
    $html .= '</p>';

    ?>

    <span class="price uk-text-primary uk-text-large">
    <?php
    echo esc_html(AP_Helper::format_price($price));
    ?>
    </span>
    <?php if($f_value){ ?>
        <span class="meta">
        <?php echo esc_html($f_value);?>
        </span>
    <?php } ?>

    <?php
    if($price_notice_value){
        ?>
        <div class="price_notice uk-text-meta">
            <?php echo esc_html($price_notice_value);?>
        </div>
        <?php
    }
    if (!empty($msrp)) {
        ?>
        <div class="uk-grid">
            <label class="single-price-label"><?php esc_html_e('MSRP:','advanced-product');?></label>
            <span class="price uk-text-primary uk-text-large">
            <?php
            echo esc_html(AP_Helper::format_price($msrp));
            ?>
            </span>
        </div>
        <?php
    }
} ?>
<?php if (!empty($product_type) && in_array('rental', $product_type) && !empty($rental)) { ?>
    <span class="price rental-price uk-text-primary uk-text-large">
        <?php
        echo esc_html(AP_Helper::format_price($rental));
        ?>
        <?php if(!empty($rental_unit)){?>
            <span class="rental-unit uk-text-meta"><?php echo ' / '.esc_html($rental_unit);?></span>
        <?php } ?>
    </span>
<?php } ?>
<?php if (!empty($product_type) && in_array('sold', $product_type) && !empty($price_sold)) { ?>
    <span class="price sold-price uk-text-primary uk-text-large">
        <?php
        echo esc_html($price_sold);
        ?>
    </span>
<?php } ?>
<?php if (!empty($product_type) && in_array('contact', $product_type) && !empty($price_contact)) { ?>
    <span class="price contact-price uk-text-primary uk-text-large">
        <?php
        echo esc_html($price_contact);
        ?>
    </span>
<?php }
