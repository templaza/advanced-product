<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\AP_Templates;
use Advanced_Product\Helper\AP_Helper;

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
            <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('full', array('data-uk-cover' => '')); ?>
            </a>
            <a href="<?php the_permalink(); ?>" class="product-more-infor uk-position-bottom">
                <span class="product-more-infor__text"><?php esc_attr_e('More Product Info','advanced-product');?></span><i class="fas fa-info-circle"></i>
            </a>
        </div>
        <div class="ap-quickview-content">
            <div class="uk-padding">
                <h2 class="ap-quickview-product_title entry-title"><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2>
                <?php
                $msrp   = get_field('ap_price_msrp', get_the_ID());
                $price  = get_field('ap_price', get_the_ID());

                if (!empty($price)) {

                    $html = '<div class="ap-pricing">';
                    $html .= sprintf('<span class="ap-price"><b> %s</b> %s </span>',
                        esc_html__(' ', 'advanced-product'), AP_Helper::format_price($price));
                    if (!empty($msrp)) {
                        $html .= sprintf('<span class="ap-price-msrp"> %s  %s </span>',
                            esc_html__('MSRP:', 'advanced-product'), AP_Helper::format_price($msrp));
                    }
                    $html .= '</div>';

                    echo balanceTags($html);
                }  ?>
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
