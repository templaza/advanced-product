<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;

?>
<?php
$show_date          = get_option('options_ap_show_date');
$show_date          = filter_var($show_date, FILTER_VALIDATE_BOOLEAN);
$show_author        = get_option('options_ap_show_author');
$show_author        = filter_var($show_author, FILTER_VALIDATE_BOOLEAN);
$show_category      = get_option('options_ap_show_category');
$show_category      = filter_var($show_category, FILTER_VALIDATE_BOOLEAN);
$show_post_view     = get_option('options_ap_show_post_view');
$show_post_view     = filter_var($show_post_view, FILTER_VALIDATE_BOOLEAN);
$show_comment_count = get_option('options_ap_show_comment_count');
$show_comment_count = filter_var($show_comment_count, FILTER_VALIDATE_BOOLEAN);
?>
<div class="uk-text-meta">
    <?php if ($show_date){ ?>
        <span class="ap-date uk-margin-small-right"><i class="far fa-calendar-check"></i> <?php echo esc_attr(get_the_date()); ?></span>
    <?php } ?>
    <?php if($show_author){ ?>
        <span class="author uk-link-text"><i class="far fa-user"></i> <?php echo get_the_author_posts_link();?></span>
    <?php } ?>
    <?php if($show_category && get_the_category_list() ){ ?>
        <span class="category uk-margin-small-right"><i class="far fa-folder"></i> <?php the_category(', '); ?></span>
    <?php } ?>
    <?php if ($show_comment_count){ ?>
        <?php $templaza_comment_count = wp_count_comments(get_the_ID());
        if ($templaza_comment_count->approved) {
            ?>
            <span class="comment_count uk-margin-small-right">
                <i class="far fa-comment"></i>
                <?php
                $templaza_comment_count = wp_count_comments(get_the_ID());
                if ($templaza_comment_count->approved == ''|| $templaza_comment_count->approved < 2) {
                    echo esc_html__('Comment:', 'advanced-product').' '.esc_html($templaza_comment_count->approved);
                }else{
                    echo esc_html__('Comments:', 'advanced-product').' '.esc_html($templaza_comment_count->approved);
                }
                ?>
            </span>
        <?php } ?>
    <?php } ?>
    <?php if($show_post_view):?>
        <span class="views uk-margin-small-right">
            <i class="far fa-eye"></i>
            <?php
            $count_key = 'post_views_count';
            $count = get_post_meta(get_the_ID(), $count_key, true);
            if ($count == '' || empty($count)) { // If such views are not
                delete_post_meta(get_the_ID(), $count_key);
                add_post_meta(get_the_ID(), $count_key, '0');
                echo esc_html__('View: 0', 'advanced-product'); // return value of 0
            }else{
                echo esc_html__('Views:', 'advanced-product').' '.$count;
            }
            ?>
        </span>
    <?php endif; ?>
</div>