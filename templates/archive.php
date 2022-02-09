<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\AP_Templates;
use Advanced_Product\Helper\AP_Helper;
use Advanced_Product\Helper\AP_Custom_Field_Helper;

?>

<?php
if ( have_posts()) {
    AP_Templates::load_my_layout('archive.content');

    the_posts_pagination( array(
        'type' => 'plain',
        'mid_size' => 2,
        'prev_text' => ent2ncr('<i class="fa fa-angle-double-left"></i>'),
        'next_text' => ent2ncr('<i class="fa fa-angle-double-right"></i>'),
        'screen_reader_text' => '',
    ) );
    ?>
<?php
}
?>