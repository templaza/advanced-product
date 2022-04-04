<?php

defined('ADVANCED_PRODUCT') or exit();

if($field) {

    $field_search   = $field;
    if($field['type'] == 'taxonomy'){
        $field_search['field_type'] = isset($field['s_type']) ? $field['s_type'] : $field['field_type'];
    }else {
        $field_search['type'] = isset($field['s_type']) ? $field['s_type'] : $field['type'];
    }
    // Replace id
    if(isset($field_search['id'])) {
        $field_search['id'] = '';
    }

//    $field_search['value']   = apply_filters('advanced-product/field/search_form/load_value/type='.$field_search['type'] , $field_search['value'], $field['field_group'], $field_search );
//    $field_search   = apply_filters('advanced-product/field/search_form/update_field/type='.$field_search['type'] , $field_search, $field['field_group'] );

    // Override html from acf field rendered
    ob_start();
    do_action('acf/create_field', $field_search);
    $html_field = ob_get_contents();
    ob_end_clean();

    $html_field = trim($html_field);

    $html_field = preg_replace('/<input type="hidden"(\s+[^>]*)?[\/]?>/ius', '', $html_field);
    ?>
    <div class="uk-margin">
        <?php if(!isset($field['s_show_label']) || (isset($field['s_show_label']) && $field['s_show_label'])){?>
        <label class="search-label"><?php echo $field['label']; ?></label>
        <?php } ?>
        <div class="uk-form-controls">
            <?php echo $html_field;?>
        </div>
    </div>
    <?php
}