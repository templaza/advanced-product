<?php

defined('ADVANCED_PRODUCT') or exit();

if($field) {

    $f_value        = isset($field['value'])?$field['value']:array();
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

    if(!empty($f_value)){
        $s_range_to     = isset($f_value[1])?$f_value[1]:$s_range_to;
        $s_range_from   = isset($f_value[0])?$f_value[0]:$s_range_from;
    }
    ?>
    <div class="uk-margin ap-search-item">
        <?php if(!isset($field['s_show_label']) || (isset($field['s_show_label']) && $field['s_show_label'])){?>
            <label class="search-label"><?php echo __($field['label'],'advanced-product'); ?></label>
        <?php } ?>
        <div class="uk-form-controls">
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