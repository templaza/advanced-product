<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\FieldHelper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;
use Advanced_Product\Helper\AP_Custom_Taxonomy_Helper;

$options    = array();


//$fields = AP_Custom_Field_Helper::get_custom_fields();

$widget_heading_style       = isset($options['widget_box_heading_style'])?$options['widget_box_heading_style']:'';
$autoshowroom_detail_make   = isset($options['autoshowroom_Detail_show_make'])?(bool) $options['autoshowroom_Detail_show_make']:true;
$autoshowroom_detail_model = isset($options['autoshowroom_Detail_show_model'])?(bool) $options['autoshowroom_Detail_show_model']:true;

?>
<div class="widget <?php echo esc_attr($widget_heading_style);?> ap-box">
    <div class="widget-content">
        <h3 class="widget-title"><span><?php esc_html_e('Specifications', 'advanced-product'); ?></span>
        </h3>
        <div class="ap-specs">
        <?php if ($autoshowroom_detail_model || $autoshowroom_detail_make == 'yes') : ?>
<!--            <div class="ap-specs">-->
                <?php if ($autoshowroom_detail_make == 'yes') { ?>
                    <div class="uk-grid-small" data-uk-grid>
                        <label class="uk-width-2-5"><?php esc_html_e('Branch', 'advanced-product'); ?></label>
                        <span class=" uk-width-expand">
                                <?php
                                $branches = wp_get_post_terms(get_the_ID(), 'ap_branch');
                                foreach ($branches as $branch) {
                                    $ve_make = $branch->slug;
                                    echo esc_attr($branch->name);
                                }
                                ?>
                        </span>
                    </div>
                <?php } ?>
                <?php if ($autoshowroom_detail_model == 'yes') { ?>
                    <div class="uk-grid-small" data-uk-grid>
                        <label class="uk-width-2-5"><?php esc_html_e('Category', 'advanced-product'); ?></label>
                        <span class=" uk-width-expand">
                                <?php $categories = wp_get_post_terms(get_the_ID(), 'ap_category');
                                foreach ($categories as $category) {
                                    echo esc_attr($category->name);
                                }
                                ?>
                            </span>
                    </div>
                <?php } ?>
                <?php
                $custom_categories  = AP_Custom_Taxonomy_Helper::get_taxonomies();
                if($custom_categories){
                    foreach($custom_categories as $custom_category){
                        $slug   = get_field('slug', $custom_category -> ID);

                        $term   = get_term(get_field($slug, get_the_ID()), $slug);
//                        $categories = wp_get_post_terms(get_the_ID(), $slug);
                        if($term && isset($term -> name)) {
                            ?>
                            <div class="uk-grid-small" data-uk-grid>
                                <label class="uk-width-2-5"><?php echo $custom_category->post_title; ?></label>
                                <span class="uk-width-expand">
                                <?php
                                //                                foreach ($categories as $category) {
                                //                                    echo esc_attr($category->name);
                                //                                }
                                echo $term->name;
                                ?>
                            </span>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
<!--            </div>-->
        <?php endif; ?>
        <?php
        // Display custom field in specifications

//        var_dump(get_fields(get_the_ID()));

        $fields = FieldHelper::get_fields_by_group('specs', array('post_type' => get_post_type()));
        if($fields && count($fields)){
        ?>
<!--        <div class="ap-specs ap-item-des">-->
        <?php
            foreach($fields as $field){
                $value = get_field($field['name'], get_the_ID());
                if (!$value) {
                    continue;
                }
                ?>
                <div class="uk-grid-small" data-uk-grid>
                    <label class="uk-width-2-5"><?php echo $field['label']; ?></label>
                    <span class="<?php echo $field['name'];?> uk-width-expand"><?php echo the_field($field['name'], get_the_ID()); ?></span>
                </div>
        <?php } ?>
<!--        </div>-->
        <?php
        }
//        echo balanceTags(tz_autoshowroom_get_vehicle_specs(get_the_ID(), 'all'));
        ?>
        </div>
        <?php
        if (class_exists('Comment_Rating_Output')):
            $average_rating = get_post_meta(get_the_ID(), 'tz-average-rating', true);
            if (empty($average_rating)) {
                $average_rating = 0;
            }
            echo '<div class="tz-average-rating"><div class="tz-rating tz-rating-' . esc_attr($average_rating) . '"></div></div>';
        endif;
        ?>
    </div>
</div>
