<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\AP_Templates;
use Advanced_Product\Helper\AP_Helper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

if(AP_Helper::get_page_id('inventory') != get_the_ID()) {
    get_header('advanced-product');
}
do_action( 'advanced_product_before_main_content' );
?>

<?php if ( have_posts()) { ?>
    <div class="uk-container uk-container-large">
    <?php
    AP_Templates::load_my_layout('archive.content');

    the_posts_pagination( array(
        'type' => 'plain',
        'mid_size' => 2,
        'prev_text' => ent2ncr('<i class="fa fa-angle-double-left"></i>'),
        'next_text' => ent2ncr('<i class="fa fa-angle-double-right"></i>'),
        'screen_reader_text' => '',
    ) );
    ?>
    </div>
<?php
}
if(AP_Helper::get_page_id('inventory') != get_the_ID()) {
    get_footer('advanced-product');
}
?>