<?php

use Advanced_Product\AP_Functions;
use Advanced_Product\AP_Templates;

defined('ADVANCED_PRODUCT') or exit();

$options    = array();
$autoshowroom_video = get_post_meta(get_the_ID(), 'video',true);
$autoshowroom_gallery = get_post_meta(get_the_ID(), 'images');
?>
<div class="ap-media entry-image full-image uk-margin-bottom uk-container-expand">
    <?php
    $autoshowroom_video = get_post_meta(get_the_ID(), 'video',true);
    $autoshowroom_gallery = get_post_meta(get_the_ID(), 'images', true);
    if ((!empty($autoshowroom_video) && !empty($autoshowroom_gallery)) ||
        (empty($autoshowroom_video) && !empty($autoshowroom_gallery))) {
        AP_Templates::load_my_layout('single.media.gallery');
    } elseif (!empty($autoshowroom_video) && empty($autoshowroom_gallery)) {
        AP_Templates::load_my_layout('single.media.video');
    } else {
        AP_Templates::load_my_layout('single.media.image');
    }
    ?>
</div>