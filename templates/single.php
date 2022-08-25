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
                $show_compare_button    = get_field('ap_show_compare_button', 'option');
                $show_compare_button= $show_compare_button!==false?(bool)$show_compare_button:true;
                if($show_compare_button) {
                    AP_Templates::load_my_layout('shortcodes.advanced-product.compare-button', true, false,
                        array('atts' => array('id' => get_the_ID())));
                }
                ?>
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
