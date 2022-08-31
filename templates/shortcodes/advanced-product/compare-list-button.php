<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Product_Helper;

extract($args);

$compare_list   = AP_Product_Helper::get_compare_product_ids_list();
$has_compare    = !empty($compare_list)?true:false;

?>
<a href="javascript:" class="uk-button uk-button-primary ap-compare-list-btn<?php echo $has_compare?' ap-compare-has-product':' uk-hidden';?>" data-ap-compare-list-button>
    <i class="fas fa-clipboard-list js-ap-icon"></i>
    <span class="js-ap-text"><?php _e('Compare list', AP_Functions::get_my_text_domain()); ?></span>
    <span class="uk-badge uk-background-default uk-text-emphasis ap-compare-count" data-ap-compare-count><?php echo $has_compare?count($compare_list):0; ?></span>
</a>