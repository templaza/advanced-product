<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

// Get custom fields
$fields     = AP_Custom_Field_Helper::get_custom_fields(array('ap_price'));
if(!empty($fields)){
    ?>
    <div class="ap-specification uk-text-meta uk-text-emphasis">
        <?php foreach($fields as $field){

            $f_attr             = AP_Custom_Field_Helper::get_custom_field_option_by_id($field -> ID);
            $f_value            = (!empty($f_attr) && isset($f_attr['name']))?get_field($f_attr['name']):null;
            $show_in_listing    = AP_Custom_Field_Helper::get_field_display_flag('show_in_listing', $field -> ID);
            $show_in_listing    = empty($f_value)?false:$show_in_listing;

            if($show_in_listing){
                ?>
                <div class="uk-grid-small" data-uk-grid>
                    <span class="ap-field-label uk-width-expand" data-uk-leader><?php echo $f_attr['label']; ?>:</span>
                    <?php

                if($f_attr['type'] == 'file'){
                    $file_url   = '';
                    $f_value    = get_field($f_attr['name'], get_the_ID());
                    if(is_array($f_value)){
                        $file_url   = $f_value['url'];
                    }elseif(is_numeric($f_value)){
                        $file_url   = wp_get_attachment_url($f_value);
                    }else{
                        $file_url   = $f_value;
                    }
                    ?>
                    <a href="<?php echo esc_attr($file_url); ?>" download><?php
                        echo esc_html__('Download', AP_Functions::get_my_text_domain())?></a>
                    <?php
                }else{
                    ?>
                    <span><?php echo $f_value; ?></span>
                <?php } ?>
                </div>
            <?php }
        } ?>
    </div>
<?php }?>