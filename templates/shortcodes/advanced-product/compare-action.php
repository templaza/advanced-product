<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Product_Helper;

extract($args);

$compare_list   = AP_Product_Helper::get_compare_product_ids_list();
$pid            = isset($pid)?$pid:get_the_ID();
$has_compare    = (!empty($compare_list) && in_array($pid, $compare_list))?true:false;

?>
<li><a href="" class="uk-icon-button" data-uk-icon="icon: trash; ratio: 0.85" data-uk-tooltip="<?php
_e('Remove this product', AP_Functions::get_my_text_domain());?>" data-ap-compare-delete-button="<?php
    echo $pid;?>"></a></li>
<?php
if(isset($actions) && !empty($actions)){
    foreach($actions as $_action){
        echo $_action;
    }
}
do_action('advanced-product/compare/action');
?>
