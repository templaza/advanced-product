<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

// Get custom fields
$fields     = AP_Custom_Field_Helper::get_custom_fields_display_flag_by_product_id('show_in_listing',get_the_ID());
if(!empty($fields)){
    ?>
    <div class="ap-specification uk-text-meta uk-text-emphasis">
        <?php foreach($fields as $field){

            $f_attr         = AP_Custom_Field_Helper::get_custom_field_option_by_id($field -> ID);
            $f_value        = (!empty($f_attr) && isset($f_attr['name']))?get_field($f_attr['name']):null;
            $f_icon         = isset($f_attr['icon'])?$f_attr['icon']:'';
            $f_icon_image   = isset($f_attr['icon_image']) && !empty($f_attr['icon_image'])?$f_attr['icon_image']:'';

            $show_icon  = get_field('ap_show_archive_custom_field_icon', 'option');
            ?>
            <div class="uk-grid-small" data-uk-grid>
                <span class="ap-field-label uk-width-expand" data-uk-leader><?php
                    if( !empty($f_icon) && $show_icon){
                        if($f_icon['type'] == 'uikit-icon'){
                            ?>
                            <i data-uk-icon="icon:<?php echo $f_icon['icon']; ?>;"></i>
                            <?php
                        }else if((empty($f_icon['type']) || empty($f_icon['icon'])) && !empty($f_icon_image)){
                            echo wp_get_attachment_image($f_icon_image, 'thumbnail', '',
                                array('data-uk-svg' => ''));
                        }else{
                            ?>
                            <i class="<?php echo $f_icon['icon']; ?>"></i>
                            <?php
                        }
                    }
                    ?><?php echo $f_attr['label']; ?>:</span>
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