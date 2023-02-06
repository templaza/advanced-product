<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;

extract($args);

$pid            = isset($atts['id'])?$atts['id']:0;
?>
<a href="javascript:" class="uk-icon-button" data-ap-quickview-button="<?php echo $pid?$pid:'';
?>" data-uk-tooltip="<?php echo esc_attr(__('Quick View', 'advanced-product')); ?>">
    <i class="fas fa-eye"></i>
</a>
