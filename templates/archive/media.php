<?php

defined('ADVANCED_PRODUCT') or exit();

$thumbnail  = get_the_post_thumbnail(get_the_ID(), 'large');
if(!empty($thumbnail)){
    ?>
    <div class="uk-card-media-top">
        <a href="<?php the_permalink(); ?>">
            <?php echo wp_kses($thumbnail, 'post');?>
        </a>
    </div>
<?php } ?>