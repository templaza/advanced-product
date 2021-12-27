<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\AP_Templates;
use Advanced_Product\Helper\AP_Helper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

?>

<?php
if ( have_posts()) {
    ?>
<div class="ap-list uk-child-width-1-2" data-uk-grid>
    <?php
    while (have_posts()): the_post();
        $price = get_field('ap_price', get_the_ID());
        ?>
        <div class="ap-item">
            <div class="uk-card uk-card-default">

                <?php AP_Templates::load_my_layout('archive.media'); ?>

                <div class="uk-card-body">
                    <h4 class="ap-title uk-card-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h4>
                    <?php the_excerpt(); ?>
                    <?php AP_Templates::load_my_layout('archive.custom-fields'); ?>
                </div>
                <?php AP_Templates::load_my_layout('archive.price');?>
            </div>
        </div>
        <?php
        comments_template();
    endwhile;
    ?>
</div>
<?php
}
?>