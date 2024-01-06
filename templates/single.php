<?php
defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Templates;
use Advanced_Product\AP_Functions;

get_header('advanced-product');
?>
<div class="uk-container uk-container-large">
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
</div>
<?php get_footer('advanced-product'); ?>
