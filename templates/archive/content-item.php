<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Templates;
use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Product_Helper;

$actions            = isset($args['actions'])?$args['actions']:false;
$show_compare_button= get_field('ap_show_archive_compare_button', 'option');
$show_compare_button= $show_compare_button!==false?(bool)$show_compare_button:true;
$show_compare_button= isset($args['show_archive_compare_button'])?(bool)$args['show_archive_compare_button']:$show_compare_button;

$show_quickview_button= get_field('ap_show_archive_quickview_button', 'option');
$show_quickview_button= $show_quickview_button!==false?(bool)$show_quickview_button:true;
$show_quickview_button= isset($args['show_archive_quickview_button'])?(bool)$args['show_archive_quickview_button']:$show_quickview_button;
?>
<div class="ap-item">
    <div class="uk-card uk-card-default uk-transition-toggle ap-item-inner">

        <?php do_action('advanced-product/archive/before_content');?>

        <?php AP_Templates::load_my_layout('archive.media'); ?>
        <div class="ap-item-content">
            <div class="uk-card-body ap-item-content-inner">
                <h4 class="ap-title uk-card-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h4>
                <?php
                if ( has_excerpt() ) {
                    ?>
                    <div class="ap-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                    <?php
                }
                ?>
                <?php AP_Templates::load_my_layout('archive.custom-fields'); ?>
            </div>
            <?php
            AP_Templates::load_my_layout('archive.price');
            AP_Templates::load_my_layout('archive.btn-actions');
            ?>
        </div>
        <?php do_action('advanced-product/archive/after_content');?>
    </div>
</div>
