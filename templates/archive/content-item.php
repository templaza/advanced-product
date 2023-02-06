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
    <div class="uk-card uk-card-default uk-transition-toggle">

        <?php do_action('advanced-product/archive/before_content');?>

        <?php AP_Templates::load_my_layout('archive.media'); ?>

        <div class="uk-card-body">
            <h4 class="ap-title uk-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h4>
            <?php the_excerpt(); ?>
            <?php AP_Templates::load_my_layout('archive.custom-fields'); ?>
        </div>
        <?php AP_Templates::load_my_layout('archive.price');?>

        <?php
        ob_start();
        do_action('advanced-product/archive/action', get_the_ID(), $args);
        do_action('advanced-product/archive/compare/action', get_the_ID(), $args);
        $action_html    = ob_get_contents();
        ob_end_clean();

        $action_html    = !empty($action_html)?trim($action_html):'';

        if($show_compare_button || $show_quickview_button || (isset($actions) && !empty($actions)) || !empty($action_html)){ ?>
        <div class="uk-list uk-transition-slide-right uk-position-right uk-margin-remove-top uk-margin-small-right">
            <?php if($show_compare_button){
                ?>
            <div>
                <?php
                $compare_list   = AP_Product_Helper::get_compare_product_ids_list();
                $pid            = get_the_ID();
                $has_compare    = (!empty($compare_list) && in_array($pid, $compare_list))?true:false;
                $active_text    = $has_compare?'In compare list':'Add to compare';
                ?>
                <a href="javascript:" class="uk-icon-button<?php echo $has_compare?' ap-in-compare-list':'';
                ?>" data-ap-compare-button="id: <?php the_ID();
                ?>; active_icon: fas fa-clipboard-list; icon: fas fa-balance-scale" data-uk-tooltip="<?php
            _e($active_text, 'advanced-product');?>">
                    <?php if($has_compare){?>
                        <i class="fas fa-clipboard-list js-ap-icon"></i>
                    <?php }else{?>
                        <i class="fas fa-balance-scale js-ap-icon"></i>
                    <?php }?>
            </a>
            </div>
            <?php } ?>
            <?php
            if($show_quickview_button) {
                AP_Templates::load_my_layout('shortcodes.advanced-product.quickview-button', true, false
                    , array('atts' => array('id' => get_the_ID())));
            }
            ?>
            <?php
            if(isset($actions) && !empty($actions)){
                foreach($actions as $_action){
                    echo $_action;
                }
            }
            echo $action_html;
            ?>
        </div>
        <?php } ?>

        <?php do_action('advanced-product/archive/after_content');?>
    </div>
</div>
