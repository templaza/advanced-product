<?php

use Advanced_Product\AP_Functions;

defined('ADVANCED_PRODUCT') or exit();

$options    = array();

$ap_video = get_field('ap_video', get_the_ID());
if(!empty($ap_video)){
?>
<div class="vehicle_video">
    <?php if(wp_oembed_get( $ap_video )) : ?>
        <?php echo wp_oembed_get($ap_video); ?>
    <?php else : ?>
        <?php echo balanceTags($ap_video); ?>
    <?php endif; ?>
</div>
<?php } ?>