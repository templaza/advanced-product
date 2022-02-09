<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Templates;

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
