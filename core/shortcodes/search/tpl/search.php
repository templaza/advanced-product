<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Helper\AP_Custom_Field_Helper;

$submit_text    = (isset($submit_text) && !empty($submit_text))?$submit_text:esc_html__('Search', 'advanced-product');
$submit_icon    = (isset($submit_icon) && !empty($submit_icon))?$submit_icon:'';
$_svg_bool      = filter_var($submit_icon, FILTER_VALIDATE_URL);

$submit_html    = $submit_text;
$sicon_html = '';
if(!empty($submit_icon)) {
    $sicon_html = $_svg_bool ? '<img src="'
        . $submit_icon . '" data-uk-svg>' : '<span class="' . $submit_icon . '"></span>';
}
if($submit_icon_position == 'before'){
    $submit_html    = $sicon_html.' '.$submit_html;
}elseif($submit_icon_position == 'after'){
    $submit_html    .= ' '.$sicon_html;
}
$submit_html    = trim($submit_html);

$instant        = isset($instant)?(bool) $instant:false;
$enable_ajax    = isset($enable_ajax)?(bool) $enable_ajax:true;
$update_url     = isset($update_url)?(bool) $update_url:true;

$__ap_settings  = array(
    "enable_ajax"   => $enable_ajax,
    "instant"   => $instant,
    "update_url"   => $update_url
);
$__class    = '';

if(isset($column_large) && $column_large) {
    $__class .= ' uk-child-width-1-' . $column_large.'@xl';
}
if(isset($column) && $column) {
    $__class .= ' uk-child-width-1-' . $column.'@l';
}
if(isset($column_laptop) && $column_laptop) {
    $__class .= ' uk-child-width-1-' . $column_laptop.'@m';
}
if(isset($column_tablet) && $column_tablet) {
    $__class .= ' uk-child-width-1-' . $column_tablet.'@s';
}
if(isset($column_mobile) && $column_mobile) {
    $__class .= ' uk-child-width-1-' . $column_mobile;
}
?>
<form role="search" method="get" action="<?php echo esc_url($action);
?>" class="uk-form-stacked advanced-product-search-form<?php echo $enable_ajax?' ap-ajax-filter':'';
echo $__class;
?>" data-ap-settings="<?php echo htmlspecialchars(json_encode($__ap_settings));?>"<?php
echo isset($column)?' data-uk-grid':''; ?>>
    <?php if(!isset($enable_keyword) || (isset($enable_keyword) && $enable_keyword)){?>
    <div class="field ap-search-item field-keyword">
        <?php if($show_label){?>
        <label class="search-label"><?php _e( 'Keyword:', 'advanced-product' ) ?></label>
        <?php } ?>
        <input type="search" class="search-field" placeholder="<?php
        _e( 'Search ...', 'advanced-product' ) ?>" value="<?php echo get_query_var('s') ?>" name="s" />
    </div>
    <?php } ?>
    <?php if(!empty($fields)){
        foreach ($fields as $field){

            $field['s_show_label']  = isset($show_label)?$show_label:true;

            $field['name']  = 'field['.$field['name'].']';
            if(!isset($field['value'])){
                $field['value'] = '';
            }


            $s_field_type = isset($field['s_type'])?$field['s_type']:'';
            $s_field_type = (empty($s_field_type) && isset($field['type']))?$field['type']:$s_field_type;

            if(isset($field['s_type'])){
                if(isset($field['field_type'])){
                    $field['field_type'] = $field['s_type'];
                }else {
                    $field['type'] = $field['s_type'];
                }
            }

            $file_path  = ADVANCED_PRODUCT_CORE_PATH.'/field-layouts/'.$s_field_type.'/'.$s_field_type.'.php';

            if(file_exists($file_path)){
                do_action('advanced-product/field/create_form/type='.$s_field_type, $field);
            }else {
                ?>
                <div class="uk-margin ap-search-item">
                <?php if(!isset($field['s_show_label']) || (isset($field['s_show_label']) && $field['s_show_label'])){?>
                    <label class="uk-form-label search-label" for="acf-field-ap_price"><?php echo __($field['label'],'advanced-product'); ?></label>
                <?php } ?>
                    <div class="uk-form-controls uk-position-relative">
                       <?php do_action('acf/create_field', $field); ?>
                    </div>

                </div>
                <?php
            }
        ?>

    <?php }  } ?>
    <?php if(!$enable_ajax || ($enable_ajax && !$instant)){ ?>
    <div class="uk-margin ap-search-item ap-search-button">
        <button class="car-search-submit templaza-btn uk-button uk-margin-top"><?php echo $submit_html; ?></button>
    </div>
    <?php } ?>
    <input type="hidden" name="post_type" value="ap_product">

</form>
