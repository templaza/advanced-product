<?php

defined('ADVANCED_PRODUCT') or exit();

$is_support_framework   = get_theme_support('templaza-framework');

if(!$is_support_framework){
    get_header();
?>
<div class="uk-container uk-container-large">
<?php } ?>
    <div class="templaza-ap-archive">
        <div class="uk-alert-warning" data-uk-alert>
            <p><?php _e('No products were found matching your selection.', 'advanced-product');?></p>
        </div>
    </div>
<?php if(!$is_support_framework){ ?>
</div>
<?php
    get_footer();
}
?>