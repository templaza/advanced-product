<?php

defined('ADVANCED_PRODUCT') or exit();

//if($field = $this -> field) {


$key    = $field['name'];

$s_range_step   = isset($field['s_range_step'])?$field['s_range_step']:1;
$s_range_to     = isset($field['s_range_to'])?$field['s_range_to']:0;
$s_range_from   = isset($field['s_range_from'])?$field['s_range_from']:0;

?>
    <tr class="field_option field_option_<?php echo $field['type'];
    ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e('Search Range From','advanced-product'); ?></label>
            <p><?php _e('Enter a number.','advanced-product'); ?></p>
        </td>
        <td>
            <?php
            do_action('acf/create_field', array(
                'type'	    =>	'number',
                'name'	    =>	'fields['.$key.'][s_range_from]',
                'value'     => $s_range_from,
            ));
            ?>
        </td>
    </tr>
    <tr class="field_option field_option_<?php echo $field['type'];
    ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search Range To",'advanced-product'); ?></label>
            <p><?php _e('Enter a number.','advanced-product'); ?></p>
        </td>
        <td>
            <?php
            do_action('acf/create_field', array(
                'type'	    =>	'number',
                'name'	    =>	'fields['.$key.'][s_range_to]',
                'value'     => $s_range_to,
            ));
            ?>
        </td>
    </tr>
    <tr class="field_option field_option_<?php echo $field['type'];
    ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search Range Step",'advanced-product'); ?></label>
            <p><?php _e('Enter a number.','advanced-product'); ?></p>
        </td>
        <td>
            <?php
            do_action('acf/create_field', array(
                'type'	    =>	'number',
                'name'	    =>	'fields['.$key.'][s_range_step]',
                'value'     => $s_range_step,
            ));
            ?>
        </td>
    </tr>
<?php
//}