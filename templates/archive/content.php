<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Templates;

?>
<div class="ap-list uk-child-width-1-2" data-uk-grid>
    <?php
    while (have_posts()): the_post();
        AP_Templates::load_my_layout('archive.content-item');
    endwhile;
    ?>
</div>
