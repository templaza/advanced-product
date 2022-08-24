<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Product_Helper;

extract($args);

$compare_list   = AP_Product_Helper::get_compare_product_ids_list();
$pid            = isset($atts['id'])?$atts['id']:0;
$has_compare    = (!empty($compare_list) && in_array($pid, $compare_list))?true:false;

?>
<div class="uk-transition-slide-right uk-position-right uk-margin-small-top">
    <a href="" class="uk-icon-button uk-margin-small-right" data-uk-icon="icon: trash; ratio: 0.85" data-uk-tooltip="<?php
    _e('Remove this product', AP_Functions::get_my_text_domain());?>"></a>
</div>
