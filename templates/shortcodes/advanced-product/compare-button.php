<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Product_Helper;

extract($args);

$compare_list   = AP_Product_Helper::get_compare_product_ids_list();
$pid            = isset($atts['id'])?$atts['id']:0;
$has_compare    = (!empty($compare_list) && in_array($pid, $compare_list))?true:false;

?>
<a href="javascript:" class="uk-button uk-button-default uk-width-1-1<?php echo $has_compare?' active':''; ?>" data-ap-compare-button="<?php
echo $pid?$pid:'';?>" data-ap-compare-active-icon="fas fa-clipboard-list">
    <?php if($has_compare){?>
    <i class="fas fa-clipboard-list js-ap-icon"></i>
    <span class=" js-ap-text"><?php
        _e('In compare list', AP_Functions::get_my_text_domain()); ?></span>
    <?php }else{?>
    <i class="fas fa-balance-scale js-ap-icon"></i>
    <span class=" js-ap-text"><?php
        _e('Add To Compare', AP_Functions::get_my_text_domain()); ?></span>
    <?php }?>
</a>