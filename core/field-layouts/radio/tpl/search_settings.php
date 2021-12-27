<?php

defined('ADVANCED_PRODUCT') or exit();

//if($field = $this -> field) {

//$field_types = apply_filters('acf/registered_fields', array());
?>
    <tr class="field_option field_option_<?php echo $field['type']; ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search choices",'acf'); ?></label>
            <p class="description"><?php _e("Enter your choices one per line",'acf'); ?><br />
                <br />
                <?php _e("Red",'acf'); ?><br />
                <?php _e("Blue",'acf'); ?><br />
                <br />
                <?php _e("red : Red",'acf'); ?><br />
                <?php _e("blue : Blue",'acf'); ?><br />
            </p>
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
                'type'	=>	'text',
                'name'	=>	'fields['.$key.'][s_default_value]',
                'value'	=>	$field['s_default_value'],
            ));

            ?>
        </td>
    </tr>
<?php
//}