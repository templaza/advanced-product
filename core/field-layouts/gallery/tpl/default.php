<?php

defined('ADVANCED_PRODUCT') or exit();

if(!empty($value)){
//    $ap_gallery = get_field($field['name'], get_the_ID());

?>
<div class="ap-slideshow uk-position-relative " data-uk-slideshow="animation: fade">
    <div class="uk-position-relative uk-visible-toggle">
        <ul class="uk-slideshow-items">
            <?php foreach ($value as $image) {
                ?>
                <li>
                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['title']); ?>" data-uk-cover>
                </li>
            <?php } ?>
        </ul>
        <a class="uk-position-center-left uk-position-small uk-hidden-hover uk-overlay uk-overlay-primary" href="#" data-uk-slidenav-previous data-uk-slideshow-item="previous"></a>
        <a class="uk-position-center-right uk-position-small  uk-hidden-hover uk-overlay uk-overlay-primary" href="#" data-uk-slidenav-next data-uk-slideshow-item="next"></a>

    </div>

    <div class="uk-position-relative uk-margin-small-top uk-visible-toggle" data-uk-slider>
        <ul class="uk-slider-items  uk-child-width-1-5 uk-child-width-1-5@m uk-grid uk-grid-small">
            <?php
            $d=0;
            foreach ($value as $image) {
                ?>
                <li data-uk-slideshow-item="<?php echo esc_attr($d);?>">
                    <a href="#">
                        <img src="<?php echo esc_url($image['url']); ?>" width="180" alt="<?php echo esc_attr($image['title']); ?>">
                    </a>
                </li>
                <?php
                $d++;
            } ?>
        </ul>
        <a class="uk-position-center-left uk-hidden-hover uk-position-small uk-overlay uk-overlay-primary" href="#" data-uk-slidenav-previous data-uk-slider-item="previous"></a>
        <a class="uk-position-center-right  uk-hidden-hover uk-position-small uk-overlay uk-overlay-primary" href="#" data-uk-slidenav-next data-uk-slider-item="next"></a>
    </div>
</div>
<?php } ?>