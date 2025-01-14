<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Product_Helper;

extract($args);

$compare_list   = AP_Product_Helper::get_compare_product_ids_list();
$pid            = isset($atts['id'])?$atts['id']:0;
$has_compare    = (!empty($compare_list) && in_array($pid, $compare_list))?true:false;

?>
<a href="javascript:" class=" uk-margin-small-top uk-display-block uk-width-1-1<?php echo $has_compare?' ap-in-compare-list':'';
?>" data-ap-compare-button="id: <?php echo $pid?$pid:'';?>; active_icon: fas fa-clipboard-list">
    <?php if($has_compare){?>
    <i class="fas fa-clipboard-list js-ap-icon"></i>
    <span class=" js-ap-text"><?php
        _e('In compare list', 'advanced-product'); ?></span>
    <?php }else{?>
        <i class="fas fa-not-equal"></i>
    <span class=" js-ap-text"><?php
        _e('Add To Compare', 'advanced-product'); ?></span>
    <?php }?>
</a>