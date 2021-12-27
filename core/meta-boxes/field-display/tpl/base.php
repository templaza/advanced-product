<?php

defined('ADVANCED_PRODUCT') or exit();

global $post_id;
$fields = $this -> register_fields();

?>

<div class="field_form">
    <?php
    if( is_array($fields) ){ foreach( $fields as $field ){

        // if they didn't select a type, skip this field
        if( !$field || !$field['type'] || $field['type'] == 'null' )
        {
            continue;
        }


        // set value
        if( !isset($field['value']) )
        {
//            $field['value'] = apply_filters('acf/load_value', false, $post_id, $field);
//            $field['value'] = apply_filters('acf/format_value', $field['value'], $post_id, $field);
            $field['value'] = get_post_meta($post_id, $field['name'], true);
        }

        // required
        $required_class = "";
        $required_label = "";

        if( $field['required'] )
        {
            $required_class = ' required';
            $required_label = ' <span class="required">*</span>';
        }

        echo '<div id="acf-' . $field['name'] . '" class="field field_type-' . $field['type'] . ' field_key-' . $field['key'] . $required_class . '" data-field_name="' . $field['name'] . '" data-field_key="' . $field['key'] . '" data-field_type="' . $field['type'] . '">';

        echo '<p class="label">';
        echo '<label for="' . $field['id'] . '">' . $field['label'] . $required_label . '</label>';
        echo $field['instructions'];
        echo '</p>';

        $field['name'] = $this -> get_meta_box_name().'[' . $field['name'] . ']';
        do_action('acf/create_field', $field, $post_id);

        echo '</div>';

    } }
    ?>
</div>