<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Templates;
use Advanced_Product\AP_Functions;

$options        = array();

$stock_number = get_post_meta(get_the_ID(),'autoshowroom_stock_number_manually',true);
$showcompare = isset($options['autoshowroom_Detail_show_compare'])?(bool) $options['autoshowroom_Detail_show_compare']:true;
$showbrochure = isset($options['autoshowroom_Detail_show_brochure'])?(bool) $options['autoshowroom_Detail_show_brochure']:true;

$showmsrp = isset($options['autoshowroom_Detail_show_msrp'])?(bool) $options['autoshowroom_Detail_show_msrp']:true;
?>
<!--<div data-uk-grid>-->
<!--    <div class="uk-width-expand@m uk-first-column">-->
<!--        <h1 class="vehicle-title">--><?php //the_title(); ?><!--</h1>-->
<!--        <div class="vehicle_url" style="display: none">--><?php //the_permalink(); ?><!--</div>-->
<!--        <div class="vehicle-btn">-->
<!--            --><?php //if ($stock_number != '') { ?>
<!--                <span class="btn-stock">-->
<!--                        --><?php //esc_html_e('Stock# ', 'autoshowroom'); ?>
<!--                    <strong>--><?php //esc_html_e($stock_number, 'autoshowroom'); ?><!--</strong>-->
<!--                    </span>-->
<!--            --><?php //} ?>
<!--            --><?php //if ($showcompare == true) { ?>
<!--                <span class="btn-function btn_detail_compare"-->
<!--                      data-images="--><?php //the_post_thumbnail_url('medium'); ?><!--"-->
<!--                      data-id="--><?php //echo esc_attr(get_the_ID()); ?><!--"-->
<!--                      data-text="--><?php //esc_html_e('In Compare List', 'autoshowroom'); ?><!--">-->
<!--                            <i class="fa fa-car"></i>-->
<!--                            --><?php //esc_html_e('Add to Compare', 'autoshowroom'); ?>
<!--                        </span>-->
<!--            --><?php //} ?>
<!--            --><?php //if ($showbrochure == 'yes' && !empty($bruchure['url'])) { ?>
<!--                <a href="--><?php //echo esc_url($bruchure['url']); ?><!--">-->
<!--                    <i class="fa fa-file-pdf-o"></i>-->
<!--                    --><?php //esc_html_e('Car Brochure', 'autoshowroom'); ?>
<!---->
<!--                </a>-->
<!--            --><?php //} ?>
<!--        </div>-->
<!---->
<!--        --><?php //AP_Templates::load_my_layout('single.content-media'); ?>
<!---->
<!--        <div class="vehicle-content">-->
<!--            <div class="vehicle-content-tab">-->
<!--                --><?php
//                the_content();
////                echo apply_filters('the_content', get_field('content'));
//                ?>
<!--            </div>-->
<!---->
<!--            <div class="autoshowroom-comment-content">-->
<!--                --><?php //comments_template('', true); ?>
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="uk-width-1-3@m autoshowroom-sidebar templaza-sidebar">-->
<!--        <aside class="widget-area">-->
<!--            --><?php
//            AP_Templates::load_my_layout('single.price');
//            ?>
<!--            --><?php
//            AP_Templates::load_my_layout('single.content-specification');
//            ?>
<!--        </aside>-->
<!--    </div>-->
<!--</div>-->
<div class="ap-single uk-article">
    <div class="" data-uk-grid>
        <div class="uk-width-expand@m">
            <?php AP_Templates::load_my_layout('single.media'); ?>

            <h1 class="uk-article-title ap-heading"><?php the_title(); ?></h1>

            <?php AP_Templates::load_my_layout('single.meta');?>

            <div class="uk-margin-medium-top"><?php the_content(); ?></div>

            <div class="ap-comment-content">
                <?php comments_template('', true); ?>
            </div>
        </div>
        <div class="uk-width-1-3@m ap-sidebar">
            <aside class="widget-area">
                <?php
                AP_Templates::load_my_layout('single.price');
                ?>
                <?php
                AP_Templates::load_my_layout('single.custom-fields');
                ?>
            </aside>
        </div>
    </div>
</div>
