<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Templates;

extract($args);

if(isset($product) && !empty($product)){
    do_action('advanced-product/quickview/before_content');
?>
    <div class="uk-grid-collapse uk-child-width-1-2@s" data-uk-grid>
            <?php
            $options    = array();
            $autoshowroom_video = get_post_meta(get_the_ID(), 'video',true);
            $autoshowroom_gallery = get_post_meta(get_the_ID(), 'images');
            ?>
        <div class="ap-quickview-media uk-cover-container">
            <?php the_post_thumbnail('full', array('data-uk-cover' => '')); ?>
        </div>
        <div class="ap-quickview-content">
            <div class="uk-padding">
                <h1 class="ap-quickview-product_title entry-title"><?php the_title();?></h1>
                <div class="uk-margin-medium-top"><?php the_content(); ?></div>
            </div>
        </div>
    </div>
<?php
    do_action('advanced-product/quickview/after_content');
} ?>
