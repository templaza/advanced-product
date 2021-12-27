<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Helper\AP_Custom_Field_Helper;

if($field) {
?>
    <tr class="field_option field_option_<?php echo $field['type']; ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search choices",'acf'); ?></label>
            <p><?php _e("Enter each choice on a new line.",'acf'); ?></p>
            <p><?php _e("For more control, you may specify both a value and label like this:",'acf'); ?></p>
            <p><?php _e("red : Red",'acf'); ?><br /><?php _e("blue : Blue",'acf'); ?></p>
        </td>
        <td>
            <?php
            do_action('acf/create_field', array(
                'type'	    =>	'textarea',
                'name'	    =>	'fields['.$key.'][s_choices]',
                'value' => $field['s_choices'],
            ));
            ?>
        </td>
    </tr>
    <tr class="field_option field_option_<?php echo $field['type']; ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search Default Value",'acf'); ?></label>
            <p class="description"><?php _e("Enter each default value on a new line",'acf'); ?></p>
        </td>
        <td>
            <?php

            do_action('acf/create_field', array(
                'type'	=>	'textarea',
                'name'	=>	'fields['.$key.'][s_default_value]',
                'value'	=>	$field['s_default_value'],
            ));

            ?>
        </td>
    </tr>
    <tr class="field_option field_option_<?php echo $field['type'];
    ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search Meta Query Compare", $this -> text_domain); ?></label>
            <!--            <p class="description">--><?php //_e("Enter each default value on a new line",'acf'); ?><!--</p>-->
        </td>
        <td>
            <?php

            do_action('acf/create_field', array(
                'type'	=>	'select',
                'name'	=>	'fields['.$key.'][s_meta_query_compare]',
                'value'	=>	$field['s_meta_query_compare'],
                'choices' => AP_Custom_Field_Helper::get_meta_query_compares(),
                'default_value' => '=',
            ));

            ?>
        </td>
    </tr>
<?php
}