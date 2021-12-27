<?php

use Advanced_Product\AP_Functions;

defined('ADVANCED_PRODUCT') or exit();

$ap_video   = get_field('ap_video', get_the_ID());
$ap_gallery = get_field('ap_gallery', get_the_ID());

if (isset($ap_video) && !empty($ap_video)) {
    preg_match("/iframe/", $ap_video, $output_array);
    if ($output_array && !empty($output_array)) {
        preg_match("/src=\"([^\"]+)\"/", $ap_video, $output_array);
        $ap_video = $output_array[1];
    }
}

if(!empty($ap_gallery)){
?>
<div data-uk-slideshow="animation: fade">

    <div class="uk-position-relative uk-visible-toggle">
        <ul class="uk-slideshow-items uk-margin-small-bottom" data-uk-lightbox="animation: slide">
            <?php foreach ($ap_gallery as $image) {
                ?>
                <li data-src="<?php echo esc_url($image['url']); ?>">
                    <a href="<?php echo esc_url($image['url']); ?>"><img src="<?php echo esc_url($image['url']); ?>"
                            alt="<?php echo esc_attr($image['title']); ?>"/></a>
                </li>
            <?php } ?>
        </ul>
        <a class="uk-position-center-left uk-position-small uk-hidden-hover uk-overlay uk-overlay-primary" href="#" data-uk-slidenav-previous data-uk-slideshow-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover uk-overlay uk-overlay-primary" href="#" data-uk-slidenav-next data-uk-slideshow-item="next"></a>
        <?php if(!empty($ap_video)){ ?>
            <div class="uk-position-top-right uk-position-small uk-overlay uk-overlay-primary uk-padding-small ap-video-button" data-uk-lightbox>
                <a class="" href="<?php
                echo esc_url($ap_video); ?>"><i class="fas fa-video"></i></a>
            </div>
        <?php } ?>
    </div>

    <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" data-uk-slider>
        <ul class="uk-slider-items uk-child-width-1-3@s uk-child-width-1-5@m uk-grid uk-grid-small uk-margin-remove-bottom">
            <?php foreach ($ap_gallery as $i => $image) { ?>
                <li data-uk-slideshow-item="<?php echo $i; ?>">
                    <img src="<?php echo esc_url($image['sizes']['large']); ?>" alt="<?php
                    echo esc_attr($image['title']); ?>"/>
                </li>
            <?php } ?>
        </ul>
        <a class="uk-position-center-left uk-position-small uk-hidden-hover uk-overlay uk-overlay-primary" href="#" data-uk-slidenav-previous data-uk-slider-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover uk-overlay uk-overlay-primary" href="#" data-uk-slidenav-next data-uk-slider-item="next"></a>
    </div>
</div>
<?php } ?>