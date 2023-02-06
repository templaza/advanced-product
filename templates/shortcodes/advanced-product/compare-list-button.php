<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Product_Helper;

extract($args);

$compare_count  = AP_Product_Helper::get_compare_product_count();
$has_compare    = $compare_count?true:false;

?>
<a href="javascript:" class="uk-button uk-button-primary ap-compare-list-btn<?php echo $has_compare?' ap-compare-has-product':' uk-hidden';?>" data-ap-compare-list-button>
    <i class="fas fa-clipboard-list js-ap-icon"></i>
    <span class="js-ap-text"><?php _e('Compare list', 'advanced-product'); ?></span>
    <span class="uk-badge uk-background-default uk-text-emphasis ap-compare-count" data-ap-compare-count><?php echo $compare_count; ?></span>
</a>