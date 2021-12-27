<?php

defined('ADVANCED_PRODUCT') or exit();

if($field) {


    $field_search   = $field;
    if($field['type'] == 'taxonomy'){
        $field_search['field_type'] = isset($field['s_type']) ? $field['s_type'] : $field['field_type'];
    }else {
        $field_search['type'] = isset($field['s_type']) ? $field['s_type'] : $field['type'];
    }

    // Override html from acf field rendered
    ob_start();
    do_action('acf/create_field', $field_search);
    $html_field = ob_get_contents();
    ob_end_clean();

    $html_field = trim($html_field);

    $html_field = preg_replace('/<input type="hidden"(\s+[^>]*)?[\/]?>/ius', '', $html_field);
    ?>
    <div class="uk-margin">
        <label><?php echo $field['label']; ?></label>
        <div class="uk-form-controls">
            <?php echo $html_field;?>
        </div>
    </div>
    <?php
}