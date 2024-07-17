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

    // Override html from acf field rendered
    ob_start();
    do_action('acf/create_field', $field_search);
    $html_field = ob_get_contents();
    ob_end_clean();

    $html_field = trim($html_field);

    $html_field = preg_replace('/<input type="hidden"(\s+[^>]*)?[\/]?>/ius', '', $html_field);
    if(preg_match('/(<input.*?class=".*?)("(\s+[^>]*)?[\/]?>)/ius', $html_field)) {
        $html_field = preg_replace('/(<input.*?class=".*?)("(\s+[^>]*)?[\/]?>)/ius',
            '$1 uk-input$2', $html_field);
    }else{
        $html_field = preg_replace('/(<input type="text")((\s+[^>]*)?[\/]?>)/ius',
            '$1 class="uk-input"$2', $html_field);
    }
    $html_field = preg_replace('/(<input type=")(text")((\s+[^>]*)?[\/]?>)/ius', '$1date" name="'.
        $field['name'].'"$3', $html_field);

    ?>
    <div class="ap-search-item uk-margin" data-field_name="<?php echo isset($field['_name'])?$field['_name']:'';
    ?>" data-field_type="<?php echo isset($field['type'])?$field['type']:'';
    ?>" data-field_key="<?php echo isset($field['key'])?$field['key']:'';?>">
        <?php if(!isset($field['s_show_label']) || (isset($field['s_show_label']) && $field['s_show_label'])){?>
            <label class="search-label uk-form-label"><?php echo __($field['label'],'advanced-product'); ?></label>
        <?php } ?>
        <div class="uk-form-controls">
            <?php echo $html_field;?>
        </div>
    </div>
    <?php
}