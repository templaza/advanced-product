<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\AP_Templates;
use Advanced_Product\Helper\AP_Helper;

extract($args);

if(isset($product) && !empty($product)){
    do_action('advanced-product/quickview/before_content');

    $thumbnail  = get_the_post_thumbnail(null, 'full', array('data-uk-cover' => ''));
    $thumbnail  = !empty($thumbnail)?trim($thumbnail):$thumbnail;
    $grid_class = !empty($thumbnail)?' uk-child-width-1-2@s':'';

?>
    <div class="uk-grid-collapse uk-width-1-1<?php echo $grid_class; ?>" data-uk-grid>
            <?php
            $options    = array();
            $autoshowroom_video = get_post_meta(get_the_ID(), 'video',true);
            $autoshowroom_gallery = get_post_meta(get_the_ID(), 'images');
            ?>
        <?php if(!empty($thumbnail)){ ?>
        <div class="ap-quickview-media uk-cover-container">
            <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('full', array('data-uk-cover' => '')); ?>
                <canvas width="600" height="400"></canvas>
            </a>
            <a href="<?php the_permalink(); ?>" class="product-more-infor uk-background-muted uk-text-center uk-position-bottom">
                <span class="product-more-infor__text"><?php esc_attr_e('More Product Info','advanced-product');?></span><i class="fas fa-info-circle"></i>
            </a>
        </div>
        <?php } ?>
        <div class="ap-quickview-content">
            <div class="uk-padding">
                <h2 class="ap-quickview-product_title entry-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>
                <?php
                AP_Templates::load_my_layout('archive.price');
                ?>
                <div class="ap-quickview-excerpt"><?php the_excerpt(); ?></div>
                <?php
                AP_Templates::load_my_layout('shortcodes.advanced-product.quickview-custom-fields');
                ?>
            </div>
        </div>
    </div>
<?php
    do_action('advanced-product/quickview/after_content');
} ?>
