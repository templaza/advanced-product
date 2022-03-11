<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

// Get custom fields
//$fields     = AP_Custom_Field_Helper::get_custom_fields(array('ap_price'));
$fields     = AP_Custom_Field_Helper::get_fields_by_display_flag('show_in_listing');
if(!empty($fields)){
    ?>
    <div class="ap-specification uk-text-meta uk-text-emphasis">
        <?php foreach($fields as $field){

            $f_attr             = AP_Custom_Field_Helper::get_custom_field_option_by_id($field -> ID);
            $f_value            = (!empty($f_attr) && isset($f_attr['name']))?get_field($f_attr['name']):null;

            if(!$f_attr || $f_attr['name'] == 'ap_price' || empty($f_value)){
                continue;
            }

            ?>
            <div class="uk-grid-small" data-uk-grid>
                <span class="ap-field-label uk-width-expand" data-uk-leader><?php echo $f_attr['label']; ?>:</span>
                <?php
                $html   = apply_filters('advanced-product/field/value_html/type='.$f_attr['type'], '', $f_value, $f_attr, $field);
                if(!empty($html)){
                    echo $html;
                }elseif(is_array($f_value)){
                    $f_value    = array_values($f_value);
                    ?>
                    <span><?php echo join(',', $f_value); ?></span>
                    <?php
                }else{
                    ?>
                <span><?php echo $f_value; ?></span>
                <?php
                }
                ?>
            </div>
            <?php
        } ?>
    </div>
<?php }?>