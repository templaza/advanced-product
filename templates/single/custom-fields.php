<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\AP_Templates;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

$options    = array();

$widget_heading_style       = isset($options['widget_box_heading_style'])?$options['widget_box_heading_style']:'';
$autoshowroom_detail_make   = isset($options['autoshowroom_Detail_show_make'])?(bool) $options['autoshowroom_Detail_show_make']:true;
$autoshowroom_detail_model = isset($options['autoshowroom_Detail_show_model'])?(bool) $options['autoshowroom_Detail_show_model']:true;

$product_id     = get_the_ID();

if($fields_wgs = AP_Custom_Field_Helper::get_fields_without_group_field()){

    ob_start();
    foreach ($fields_wgs as $field) {
        AP_Templates::load_my_layout('single.custom-fields-item', true, false, array(
            'field'         => $field,
            'product_id'    => $product_id
        ));
    }
    $html   = ob_get_contents();
    ob_end_clean();

    $html   = trim($html);

    if(!empty($html)){
        ?>
<div class="widget <?php echo esc_attr($widget_heading_style);?> ap-box ap-group ap-group-empty">
    <div class="widget-content">
        <h3 class="widget-title">
            <span><?php esc_html_e('Custom Fields', 'advanced-product'); ?></span>
        </h3>
        <div class="ap-group-content"><?php echo $html; ?></div>
    </div>
</div>
        <?php
    }
}

$gfields_assigned   = AP_Custom_Field_Helper::get_group_fields_by_product();

if($gfields_assigned && count($gfields_assigned)){
    foreach ($gfields_assigned as $group) {

        $fields = AP_Custom_Field_Helper::get_fields_by_group_fields($group);
        if($fields && count($fields)) {
            ob_start();
            foreach ($fields as $field) {
                AP_Templates::load_my_layout('single.custom-fields-item', true, false, array(
                    'field'         => $field,
                    'product_id'    => $product_id
                ));
            }
            $html = ob_get_contents();
            ob_end_clean();

            $html = trim($html);
        }
        if(!empty($html)){
        ?>
<div class="widget <?php echo esc_attr($widget_heading_style);?> ap-box ap-group ap-group-<?php echo $group -> slug; ?>">
    <div class="widget-content">
        <h3 class="widget-title">
            <span><?php esc_html_e($group -> name, 'advanced-product'); ?></span>
        </h3>
        <div class="ap-group-content"><?php echo $html;?></div>
    </div>
</div>
        <?php
        }
    }
}
?>