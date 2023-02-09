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

$show_price         = AP_Custom_Field_Helper::get_field_display_flag_by_field_name('show_in_listing', 'ap_price');
$show_price_msrp    = AP_Custom_Field_Helper::get_field_display_flag_by_field_name('show_in_listing', 'ap_price_msrp');
$show_price_rental  = AP_Custom_Field_Helper::get_field_display_flag_by_field_name('show_in_listing', 'ap_rental_price');

if ((!empty($price) && $show_price) || (!empty($rental) && $show_price_rental)) {
    ?>
    <div class="uk-card-footer uk-background-primary uk-light">
        <?php
        $html   = '';
        if((!$product_type || in_array('sale', $product_type))){
            $html = sprintf('<span class="ap-price"><b> %s</b> %s </span>',
                esc_html__(' ', 'advanced-product'), AP_Helper::format_price($price));
            if (!empty($msrp) && $show_price_msrp) {
                $html .= sprintf('<span class="ap-price-msrp"> %s  %s </span>',
                    esc_html__('MSRP:', 'advanced-product'), AP_Helper::format_price($msrp));
            }
        }
        if (!empty($product_type) && in_array('rental', $product_type) && !empty($rental) && $show_price_rental) {
            $html .= sprintf('<span class="ap-price ap-price-rental"> %s %s %s </span>',
                (!empty($html)?'-':''),esc_html__('Rental:', 'advanced-product'),
                AP_Helper::format_price($rental));
            if(!empty($rental_unit)){
                $html .= sprintf('<span class="meta ap-unit">/ %s </span>',
                    esc_html($rental_unit));
            }
        }
        ?>
        <?php
        echo balanceTags($html);
        ?>
    </div>
<?php } ?>
