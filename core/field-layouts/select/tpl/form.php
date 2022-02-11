<?php

defined('ADVANCED_PRODUCT') or exit();

//if($field = $this -> field) {

if(isset($field['s_from_to']) && $field['s_from_to'] == 1){

    $field_from = $field;
    $field_from['name']            .= '[]';
    $field_from['type']             = isset($field['s_type'])?$field['s_type']:$field['type'];
    $field_from['choices']          = isset($field['s_choices_from'])?$field['s_choices_from']:array();
    $field_from['default_value']    = isset($field['s_default_value_from'])?$field['s_default_value_from']:$field['default_value'];

    $field_from['value']   = apply_filters('acf/load_value/type='.$field_from['type'] , $field_from['default_value'], $field_from['field_group'], $field_from );
    $field_from   = apply_filters('acf/update_field/type='.$field_from['type'] , $field_from, $field['field_group'] );

    // Override html from acf field rendered
    ob_start();
    do_action('acf/create_field', $field_from);
    $html_field = ob_get_contents();
    ob_end_clean();

    $field_to = $field;
    $field_to['name']          .= '[]';
    $field_to['type']           = isset($field['s_type'])?$field['s_type']:$field['type'];
    $field_to['choices']        = isset($field['s_choices_to'])?$field['s_choices_to']:array();
    $field_to['default_value']  = isset($field['s_default_value_to'])?$field['s_default_value_to']:$field['default_value'];

    $field_to['value']   = apply_filters('acf/load_value/type='.$field_to['type'] , $field_to['default_value'], $field_to['field_group'], $field_to );

    $field_to   = apply_filters('acf/update_field/type='.$field_to['type'] , $field_to, $field_to['field_group'] );

    $field_to['class']  = 'uk-margin-small-top';

    // Override html from acf field rendered
    ob_start();
    do_action('acf/create_field', $field_to);
    $html_field .= ob_get_contents();
    ob_end_clean();

}else{
    $field_search   = $field;
    if($field['type'] == 'taxonomy'){
        $field_search['field_type'] = isset($field['s_type']) ? $field['s_type'] : $field['field_type'];
    }else {
        $field_search['type'] = isset($field['s_type']) ? $field['s_type'] : $field['type'];
    }


    $field_search['choices']        = isset($field['s_choices'])?$field['s_choices']:array();
    $field_search['default_value']  = isset($field['s_default_value'])?$field['s_default_value']:$field['default_value'];

    $field_search['value']   = apply_filters('acf/load_value/type='.$field_search['type'] , $field_search['default_value'], $field['field_group'], $field_search );
    $field_search   = apply_filters('acf/update_field/type='.$field_search['type'] , $field_search, $field['field_group'] );



    // Override html from acf field rendered
    ob_start();
    do_action('acf/create_field', $field_search);
    $html_field = ob_get_contents();
    ob_end_clean();
}

$html_field = trim($html_field);

$html_field = preg_replace('/<input type="hidden"(\s+[^>]*)?[\/]?>/ius', '', $html_field);

if(($field['type'] == 'select' || (isset($field['field_type']) && $field['field_type'] == 'select'))
    && preg_match('/(<select(\s+[^>]*)?>)((.|\n)*?)(<\/select(\s+[^>]*)?>)/ius', trim($html_field))) {
    $html_field = preg_replace('/(<select(\s+[^>]*)?>)((.|\n)*?)(<\/select(\s+[^>]*)?>)/ius', '$1<option value="">'.esc_html__('All',$this -> text_domain).'</option>$3$5', $html_field);
}

    ?>
    <div class="ap-search-item uk-margin">
        <?php if(!isset($field['s_show_label']) || (isset($field['s_show_label']) && $field['s_show_label'])){?>
        <label><?php echo $field['label']; ?></label>
        <?php }?>
        <div class="uk-form-controls">
            <?php echo $html_field;?>
        </div>
    </div>
<?php
//}