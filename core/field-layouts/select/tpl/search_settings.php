<?php

defined('ADVANCED_PRODUCT') or exit();

//if($field = $this -> field) {


$key    = $field['name'];

?>
    <tr class="field-search-option-s_from_to field_option field_option_<?php echo $field['type']; ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search From-To",$this -> text_domain); ?></label>
        </td>
        <td>
            <?php
            do_action('acf/create_field', array(
                'type'	    =>	'radio',
                'name'	    =>	'fields['.$key.'][s_from_to]',
                'value'     =>  isset($field['s_from_to'])?$field['s_from_to']:0,
//                'value'     =>  $field['s_from_to'],
                'layout'	=>	'horizontal',
                'choices'   => array(
                    1	=>	__("Yes",'acf'),
                    0	=>	__("No",'acf'),
                ),
//                'default_value'	=>	'0',
            ));
            ?>
        </td>
    </tr>
    <tr class="field_option field-search-option-s_from_to_1 field_option_<?php echo $field['type'];
    ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search Choices From",$this -> text_domain); ?></label>
            <p><?php _e("Enter each choice on a new line.",'acf'); ?></p>
            <p><?php _e("For more control, you may specify both a value and label like this:",'acf'); ?></p>
            <p><?php _e("red : Red",'acf'); ?><br /><?php _e("blue : Blue",'acf'); ?></p>
        </td>
        <td>
            <?php
            do_action('acf/create_field', array(
                'type'	    =>	'textarea',
                'name'	    =>	'fields['.$key.'][s_choices_from]',
                'value'     => $field['s_choices_from'],
            ));
            ?>
        </td>
    </tr>
    <tr class="field_option field-search-option-s_from_to_1 field_option_<?php echo $field['type']; ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search Default Value From", $this -> text_domain); ?></label>
            <p class="description"><?php _e("Enter each default value on a new line",'acf'); ?></p>
        </td>
        <td>
            <?php

            do_action('acf/create_field', array(
                'type'	=>	'textarea',
                'name'	=>	'fields['.$key.'][s_default_value_from]',
                'value'	=>	$field['s_default_value_from'],
            ));

            ?>
        </td>
    </tr>
    <tr class="field_option field-search-option-s_from_to_1 field_option_<?php echo $field['type'];
    ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search Choices To",$this -> text_domain); ?></label>
            <p><?php _e("Enter each choice on a new line.",'acf'); ?></p>
            <p><?php _e("For more control, you may specify both a value and label like this:",'acf'); ?></p>
            <p><?php _e("red : Red",'acf'); ?><br /><?php _e("blue : Blue",'acf'); ?></p>
        </td>
        <td>
            <?php
            do_action('acf/create_field', array(
                'type'	    =>	'textarea',
                'name'	    =>	'fields['.$key.'][s_choices_to]',
                'value'     => $field['s_choices_to'],
            ));
            ?>
        </td>
    </tr>
    <tr class="field_option field-search-option-s_from_to_1 field_option_<?php echo $field['type'];
    ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search Default Value To",$this -> text_domain); ?></label>
            <p class="description"><?php _e("Enter each default value on a new line",'acf'); ?></p>
        </td>
        <td>
            <?php

            do_action('acf/create_field', array(
                'type'	=>	'textarea',
                'name'	=>	'fields['.$key.'][s_default_value_to]',
                'value'	=>	$field['s_default_value_to'],
            ));

            ?>
        </td>
    </tr>

    <tr class="field_option field-search-option-s_from_to_0 field_option_<?php echo $field['type'];
    ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search choices",$this -> text_domain); ?></label>
            <p><?php _e("Enter each choice on a new line.",'acf'); ?></p>
            <p><?php _e("For more control, you may specify both a value and label like this:",'acf'); ?></p>
            <p><?php _e("red : Red",'acf'); ?><br /><?php _e("blue : Blue",'acf'); ?></p>
        </td>
        <td>
            <?php
            do_action('acf/create_field', array(
                'type'	    =>	'textarea',
                'name'	    =>	'fields['.$key.'][s_choices]',
                'value'     => $field['s_choices'],
            ));
            ?>
        </td>
    </tr>
    <tr class="field_option field-search-option-s_from_to_0 field_option_<?php echo $field['type'];
    ?> field_search_option field_search_option_<?php echo $field['s_type'];?>">
        <td class="label">
            <label><?php _e("Search Default Value",$this -> text_domain); ?></label>
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
                'choices' => array(
                        '=' => __('=', $this -> text_domain),
                        '!=' => __('!=', $this -> text_domain),
                        '>' => __('>', $this -> text_domain),
                        '>=' => __('>=', $this -> text_domain),
                        '<' => __('<', $this -> text_domain),
                        '<=' => __('<=', $this -> text_domain),
                        'LIKE' => __('LIKE', $this -> text_domain),
                        'NOT LIKE' => __('NOT LIKE', $this -> text_domain),
                        'IN' => __('IN', $this -> text_domain),
                        'NOT IN' => __('NOT IN', $this -> text_domain),
                        'BETWEEN' => __('BETWEEN', $this -> text_domain),
                        'NOT BETWEEN' => __('NOT BETWEEN', $this -> text_domain),
                        'EXISTS' => __('EXISTS', $this -> text_domain),
                        'NOT EXISTS' => __('NOT EXISTS', $this -> text_domain),
                        'REGEXP' => __('REGEXP', $this -> text_domain),
                        'NOT REGEXP' => __('NOT REGEXP', $this -> text_domain),
                        'RLIKE' => __('NOT REGEXP', $this -> text_domain),
                ),
                'default_value' => '=',
            ));

            ?>
        </td>
    </tr>
<?php
//}