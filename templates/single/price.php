<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Helper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

?>
<?php
$msrp   = get_field('ap_price_msrp', get_the_ID());
$price  = get_field('ap_price', get_the_ID());

$price_id       = AP_Custom_Field_Helper::get_id_by_post_id('ap_price', get_the_ID());
$price_msrp_id  = AP_Custom_Field_Helper::get_id_by_post_id('ap_price_msrp', get_the_ID());

$show_price         = AP_Custom_Field_Helper::get_field_display_flag('show_in_listing', $price_id);
$show_price_msrp    = AP_Custom_Field_Helper::get_field_display_flag('show_in_listing', $price_msrp_id);

if (!empty($price) && $show_price) {

    $html .= '<p class="uk-background-primary uk-padding-small uk-light ap-pricing">';
    $html .= sprintf('<span class="ap-price uk-h3"><b> %s</b> %s </span>',
        esc_html__(' ', AP_Functions::get_my_text_domain()), AP_Helper::format_price($price));
    if (!empty($msrp) && $show_price_msrp) {
        $html .= sprintf('<span class="ap-price-msrp"> %s  %s </span>',
            esc_html__('MSRP:', AP_Functions::get_my_text_domain()), AP_Helper::format_price($msrp));
    }
    $html .= '</p>';

    echo balanceTags($html);
} ?>
