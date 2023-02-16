<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\Helper\AP_Custom_Field_Helper;

//// global
global $post_id, $post, $field_types;
//
// get fields
$fields = apply_filters('acf/field_group/get_fields', array(), $post->ID);

// add clone
$field = apply_filters('acf/load_field_defaults',  array(
    'key' => 'field_'.uniqid(),
    'label' => '',
    'name' => '',
    'type' => 'text',
));

if($fields && count($fields)){
    $field  = $fields[0];
}


// get name of all fields for use in field type drop down
$field_types = apply_filters('acf/registered_fields', array());


// conditional logic dummy data
$conditional_logic_rule = array(
    'field' => '',
    'operator' => '==',
    'value' => ''
);

$fake_name = $field['key'];
?>

<div id="acf_fields">
            <div class="field_form">

                <table class="acf_input widefat acf_field_form_table">
                    <tbody>
                    <tr class="field_label">
                        <td class="label">
                            <label><?php _e("Field Label",'acf'); ?><span class="required">*</span></label>
                            <p class="description"><?php _e("This is the name which will appear on the EDIT page",'acf'); ?></p>
                        </td>
                        <td>
                            <?php
                            do_action('acf/create_field', array(
                                'type'	=>	'text',
                                'name'	=>	'fields[' .$fake_name . '][label]',
                                'value'	=>	$field['label'],
                                'class'	=>	'label',
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr class="field_name">
                        <td class="label">
                            <label><?php _e("Field Name",'acf'); ?><span class="required">*</span></label>
                            <p class="description"><?php _e("Single word, no spaces. Underscores and dashes allowed",'acf'); ?></p>
                        </td>
                        <td>
                            <?php
                            ob_start();
                            do_action('acf/create_field', array(
                                'type'	=>	'text',
                                'name'	=>	'fields[' .$fake_name . '][name]',
                                'value'	=>	$field['name'],
                                'class'	=>	'name',
                            ));
                            $field_name = ob_get_contents();
                            ob_end_clean();

                            if(AP_Custom_Field_Helper::is_protected_field($post_id)){
                                $field_name = preg_replace('/(<input.*?)(\/?>)/im', '$1 readonly="readonly" $2', $field_name);
                            }
                            echo $field_name;
                            ?>
                        </td>
                    </tr>
                    <tr class="field_type">
                        <td class="label">
                            <label><?php _e("Field Type",'acf'); ?><span class="required">*</span></label>
                        </td>
                        <td>
                            <?php

                            if(AP_Custom_Field_Helper::is_protected_field($post_id)){
//                                $doc = new DOMDocument();
//                                $doc -> loadHTML($field_type);
//                                $opts   = $doc -> getElementsByTagName('option');
//                                for($i=0;$i<$opts->length;$i++){
//                                    var_dump($opts -> item($i) ->);
//                                }
//                                var_dump($field_type);
//                                var_dump($doc -> getElementsByTagName('option') ->length);
//                                var_dump($doc -> getElementsByTagName('option') ->item(0) ->attributes['value']);
//                                die(__FILE__);
//                                $field_type = preg_replace('/(<option.*? value="'.$field['type']
/*                                    .'")(\/?>)/im', '$1 readonly="readonly" $2', $field_type);*/

                                ob_start();
                                do_action('acf/create_field', array(
                                    'type'	=>	'text',
                                    'name'	=>	'fields[' .$fake_name . '][type]',
                                    'value'	=>	$field['type'],
                                ));
                                $field_type = ob_get_contents();
                                ob_end_clean();
                                $field_type = preg_replace('/(<input.*?)(\/?>)/im', '$1 readonly="readonly" $2', $field_type);
                                echo $field_type;
                            }else {

//                                ob_start();
                                do_action('acf/create_field', array(
                                    'type'		=>	'select',
                                    'name'		=>	'fields[' .$fake_name . '][type]',
//                                'name'		=>	'field_options[' .$fake_name . '][type]',
                                    'value'		=>	$field['type'],
                                    'choices' 	=>	$field_types,
                                ));
//                                $field_type = ob_get_contents();
//                                ob_end_clean();
                            }
                            ?>
                        </td>
                    </tr>
                    <tr class="field_instructions">
                        <td class="label"><label><?php _e("Field Instructions",'acf'); ?></label>
                            <p class="description"><?php _e("Instructions for authors. Shown when submitting data",'acf'); ?></p></td>
                        <td>
                            <?php
                            do_action('acf/create_field', array(
                                'type'	=>	'textarea',
                                'name'	=>	'fields[' .$fake_name . '][instructions]',
                                'value'	=>	$field['instructions'],
                                'rows'	=> 6
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr class="required">
                        <td class="label"><label><?php _e("Required?",'acf'); ?></label></td>
                        <td>
                            <?php
                            do_action('acf/create_field', array(
                                'type'	=>	'radio',
                                'name'	=>	'fields[' .$fake_name . '][required]',
                                'value'	=>	$field['required'],
                                'choices'	=>	array(
                                    1	=>	__("Yes",'acf'),
                                    0	=>	__("No",'acf'),
                                ),
                                'layout'	=>	'horizontal',
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr data-field_name="<?php echo $field['key']; ?>" class="acf-conditional_logic-show">
                        <td class="label"><label><?php _e("Icon",'acf'); ?></label></td>
                        <td>
                            <div data-field_type="ap_icon">
                                <?php
                                do_action('acf/create_field', array(
                                    'type'	=>	'ap_icon',
                                    'name'	=>	'fields['.$field['key'].'][icon]',
                                    'value'	=>	isset($field['icon'])?$field['icon']:'',
                                    'layout'	=>	'horizontal',
                                ));
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr data-field_name="<?php echo $field['key']; ?>">
                        <td class="label"><label><?php _e("Icon Image",'acf'); ?></label></td>
                        <td>
                            <div data-field_type="ap_icon">
                                <?php
                                do_action('acf/create_field', array(
                                    'type'	=>	'image',
                                    'name'	=>	'fields['.$field['key'].'][icon_image]',
                                    'value'	=>	isset($field['icon_image'])?$field['icon_image']:'',
                                    'layout'	=>	'horizontal',
                                ));

                                ?>
                            </div>
                        </td>
                    </tr>
                    <?php

                    $field['name'] = $fake_name;
                    do_action('acf/create_field_options', $field );

                    ?>
                    <tr class="">
                        <td class="label"><label><?php _e("Wrapper Attributes",'advanced-product'); ?></label></td>
                        <td>
                            <div class="acf-input-prepend"><?php _e('Width', 'advanced-product'); ?></div>
                            <div class="acf-input-append"><?php _e('%', 'advanced-product'); ?></div>
                            <?php
                            do_action('acf/create_field', array(
                                'type'	=>	'number',
                                'name'	=>	'fields['.$field['key'].'][wrapper_attribute][width]',
                                'value'	=>	isset($field['wrapper_attribute']['width'])?$field['wrapper_attribute']['width']:'',
                                'layout'	=>	'horizontal',
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr class="conditional-logic" data-field_name="<?php echo $field['key']; ?>">
                        <td class="label"><label><?php _e("Conditional Logic",'acf'); ?></label></td>
                        <td>
                            <?php
                            do_action('acf/create_field', array(
                                'type'	=>	'radio',
                                'name'	=>	'fields['.$field['key'].'][conditional_logic][status]',
                                'value'	=>	$field['conditional_logic']['status'],
                                'choices'	=>	array(
                                    1	=>	__("Yes",'acf'),
                                    0	=>	__("No",'acf'),
                                ),
                                'layout'	=>	'horizontal',
                            ));


                            // no rules?
                            if( ! $field['conditional_logic']['rules'] )
                            {
                                $field['conditional_logic']['rules'] = array(
                                    array() // this will get merged with $conditional_logic_rule
                                );
                            }

                            ?>
                            <div class="contional-logic-rules-wrapper" <?php if( ! $field['conditional_logic']['status'] ) echo 'style="display:none"'; ?>>
                                <table class="conditional-logic-rules widefat acf-rules <?php if( count($field['conditional_logic']['rules']) == 1) echo 'remove-disabled'; ?>">
                                    <tbody>
                                    <?php foreach( $field['conditional_logic']['rules'] as $rule_i => $rule ):

                                        // validate
                                        $rule = array_merge($conditional_logic_rule, $rule);


                                        // fix PHP error in 3.5.4.1
                                        if( strpos($rule['value'],'Undefined index: value in') !== false  )
                                        {
                                            $rule['value'] = '';
                                        }
                                        $conditional_logic_values   = array();

                                        ?>
                                        <tr data-i="<?php echo $rule_i; ?>">
                                            <td>
                                                <?php
                                                $choice_fields  = AP_Custom_Field_Helper::get_custom_fields_have_choices();
                                                if(!empty($choice_fields) && count($choice_fields)){
                                                    $choices    = array();
                                                    foreach ($choice_fields as $cfield){
                                                        // Exclude this field
                                                        if(isset($field['_name']) && $field['_name'] == $cfield['name']){
                                                            continue;
                                                        }
                                                        $choices[$cfield['key']]   = $cfield['label'];

                                                        ob_start();
                                                        if(isset($cfield['choices'])) {
                                                            do_action('acf/create_field', array(
                                                                'type' => 'select',
                                                                'name' => 'fields[' . $field['key'] . '][conditional_logic][rules]['
                                                                    . $rule_i . '][value]',
                                                                'class' => 'select conditional-logic-value conditional-logic-value-' . $cfield['key'],
                                                                'style' => 'display:none;',
                                                                'value' => isset($rule['value'])?$rule['value']:'',
                                                                'choices' => isset($cfield['choices']) ? $cfield['choices'] : '',
                                                            ));
                                                        }else{
                                                            do_action('acf/create_field', array(
                                                                'type' => 'text',
                                                                'name' => 'fields[' . $field['key'] . '][conditional_logic][rules]['
                                                                    . $rule_i . '][value]',
                                                                'class' => 'conditional-logic-value conditional-logic-value-' . $cfield['key'],
                                                                'style' => 'display:none;',
                                                                'value' => isset($rule['value'])?$rule['value']:'',
                                                            ));
                                                        }
//
                                                        $conditional_logic_values[] = ob_get_contents();
                                                        ob_end_clean();
                                                ?>
                                                <?php
                                                    }
                                                    do_action('acf/create_field', array(
                                                        'type'	    =>	'select',
                                                        'name'	    =>	'fields['.$field['key'].'][conditional_logic][rules]['
                                                            . $rule_i . '][field]',
                                                        'class'	    =>	'select conditional-logic-field',
                                                        'value'	    =>	$rule['field'],
                                                        'choices'	=>	$choices,
                                                    ));
                                                }else{
                                                ?>
                                                <input class="conditional-logic-field" type="hidden" name="fields[<?php echo $field['key']; ?>][conditional_logic][rules][<?php echo $rule_i; ?>][field]" value="<?php echo $rule['field']; ?>" />
                                                <?php } ?>
                                            </td>
                                            <td width="25%">
                                                <?php
                                                do_action('acf/create_field', array(
                                                    'type'	=>	'select',
                                                    'name'	=>	'fields['.$field['key'].'][conditional_logic][rules][' . $rule_i . '][operator]',
                                                    'value'	=>	$rule['operator'],
                                                    'choices'	=>	array(
                                                        '=='	=>	__("is equal to",'acf'),
                                                        '!='	=>	__("is not equal to",'acf'),
                                                    ),
                                                ));
                                                ?>
                                            </td>
                                            <td>
                                                <?php if(isset($conditional_logic_values) && !empty($conditional_logic_values)){
//                                                    foreach ($conditional_logic_values as $con_value){
//
//                                                    }
                                                    echo implode("\n", $conditional_logic_values);
                                                    ?>
                                                <?php }else{?>
                                                <input class="conditional-logic-value" type="hidden" name="fields[<?php
                                                echo $field['key']; ?>][conditional_logic][rules][<?php echo $rule_i;
                                                ?>][value]" value="<?php echo $rule['value']; ?>" />
                                                <?php } ?>
                                            </td>
                                            <td class="buttons">
                                                <ul class="hl clearfix">
                                                    <li><a class="acf-button-remove" href="javascript:;"></a></li>
                                                    <li><a class="acf-button-add" href="javascript:;"></a></li>
                                                </ul>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <ul class="hl clearfix">
                                    <li style="padding:4px 4px 0 0;"><?php _e("Show this field when",'acf'); ?></li>
                                    <li><?php do_action('acf/create_field', array(
                                            'type'	=>	'select',
                                            'name'	=>	'fields['.$field['key'].'][conditional_logic][allorany]',
                                            'value'	=>	$field['conditional_logic']['allorany'],
                                            'choices' => array(
                                                'all'	=>	__("all",'acf'),
                                                'any'	=>	__("any",'acf'),
                                            ),
                                        )); ?></li>
                                    <li style="padding:4px 0 0 4px;"><?php _e("these rules are met",'acf'); ?></li>
                                </ul>

                            </div>



                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
    <!--    </div>-->
    <!--</div>-->
</div>