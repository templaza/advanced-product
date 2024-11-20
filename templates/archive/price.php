<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Helper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

$msrp           = get_field('ap_price_msrp', get_the_ID());
$price          = get_field('ap_price', get_the_ID());
$rental         = get_field('ap_rental_price', get_the_ID());
$rental_unit    = get_field('ap_rental_unit', get_the_ID());
$product_type   = get_field('ap_product_type', get_the_ID());

$price_sold     = get_field('ap_price_sold', get_the_ID());
$price_contact  = get_field('ap_price_contact', get_the_ID());

$show_price         = AP_Custom_Field_Helper::get_field_display_flag_by_field_name('show_in_listing', 'ap_price');
$show_price_sold    = AP_Custom_Field_Helper::get_field_display_flag_by_field_name('show_in_listing', 'ap_price_sold');
$show_price_msrp    = AP_Custom_Field_Helper::get_field_display_flag_by_field_name('show_in_listing', 'ap_price_msrp');
$show_price_rental  = AP_Custom_Field_Helper::get_field_display_flag_by_field_name('show_in_listing', 'ap_rental_price');
$show_price_contact = AP_Custom_Field_Helper::get_field_display_flag_by_field_name('show_in_listing', 'ap_price_contact');

if ((!empty($price) && $show_price) || (!empty($rental) && $show_price_rental)
    || (!empty($price_contact) && $show_price_contact)
    || (!empty($price_sold) && $show_price_sold)) {
    ?>
    <div class="uk-card-footer uk-background-primary uk-light ap-item-footer">
        <?php
        $html   = '';
        if((!$product_type || in_array('sale', $product_type)) && !empty($price) && $show_price){
            $html = sprintf('<span class="ap-price"><span> %s</span> %s </span>',
                esc_html__('Price:', 'advanced-product'), AP_Helper::format_price($price));
            if (!empty($msrp) && $show_price_msrp) {
                $html .= sprintf('<span class="ap-price-msrp"> %s  %s </span>',
                    esc_html__('MSRP:', 'advanced-product'), AP_Helper::format_price($msrp));
            }
        }
        if (!empty($product_type) && in_array('rental', $product_type) && !empty($rental) && $show_price_rental) {
            $html .= sprintf('<span class="ap-price ap-price-rental"> %s <span>%s </span>%s </span>',
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

        if (!empty($product_type) && in_array('contact', $product_type) && !empty($price_contact) && $show_price_contact) {
            ?>
                <span class="ap-field-label"><?php esc_html_e('Price:','advanced-product'); ?></span>
                <span class="ap-price"><?php echo esc_html($price_contact);?></span>
        <?php }

        if (!empty($product_type) && in_array('sold', $product_type) && !empty($price_sold) && $show_price_sold) {
            ?>
                <span class="ap-field-label"><?php esc_html_e('Status:','advanced-product'); ?></span>
                <span class="ap-price"><?php echo esc_html($price_sold);?></span>
        <?php } ?>
    </div>
<?php } ?>
