<?php

defined('ADVANCED_PRODUCT') or exit();

if($field) {

    $field_search   = $field;
//    if($field['type'] == 'taxonomy'){
//        $field_search['field_type'] = isset($field['s_type']) ? $field['s_type'] : $field['field_type'];
//    }else {
//        $field_search['type'] = isset($field['s_type']) ? $field['s_type'] : $field['type'];
//    }
//    $field_search['type'] = 'text';

//    // Override html from acf field rendered
//    ob_start();
//    do_action('acf/create_field', $field_search);
//    $html_field = ob_get_contents();
//    ob_end_clean();
//
//    $html_field = trim($html_field);

/*    $html_field = preg_replace('/<input type="hidden"(\s+[^>]*)?[\/]?>/ius', '', $html_field);*/
/*    preg_match('/(<input)((\s+[^>]*)?[\/]?>)/ius', $html_field, $match);*/
//
//    var_dump($html_field);

    $symbol         = get_option('options_ap_currency_symbol', '$');
    $placement      = get_option('options_ap_symbol_placement', 'prepend');
    $s_range_step   = isset($field['s_range_step'])?$field['s_range_step']:1;
    $s_range_to     = isset($field['s_range_to'])?$field['s_range_to']:0;
    $s_range_from   = isset($field['s_range_from'])?$field['s_range_from']:0;

    $range_options  = array(
            'max'   => $s_range_to,
            'min'   => $s_range_from,
            'step'   => $s_range_step,
            'symbol'   => $symbol,
            'placement' => $placement,
    );
    ?>
    <div class="uk-margin ap-search-item">
        <?php if(!isset($field['s_show_label']) || (isset($field['s_show_label']) && $field['s_show_label'])){?>
            <label class="search-label"><?php echo __($field['label'],'advanced-product'); ?></label>
        <?php } ?>
        <div class="uk-form-controls">
<!--            --><?php //echo $html_field;?>
            <div class="ap-slider-amount" data-ap-range-slider="<?php echo esc_attr(json_encode($range_options)); ?>">
                <input type="hidden" name="<?php echo $field['name'];?>[]" data-ap-range-min value="<?php
                echo $s_range_from; ?>"/>
                <input type="hidden" name="<?php echo $field['name'];?>[]" data-ap-range-max value="<?php
                echo $s_range_to; ?>"/>
                <div class="ap-slider-range"></div>
                <div class="ap-slider-number-label">
                    <?php
                    $s_range_to     = $placement == 'append'?$s_range_to.$symbol:$symbol.$s_range_to;
                    $s_range_from   = $placement == 'append'?$s_range_from.$symbol:$symbol.$s_range_from;
                    ?>
                    <span class="from"><?php echo $s_range_from; ?></span> - <span class="to"><?php
                        echo $s_range_to;?></span>
                </div>
            </div>
        </div>
    </div>
    <?php
}