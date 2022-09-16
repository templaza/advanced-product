<?php

defined('ADVANCED_PRODUCT') or exit();
?>
<div id="ap-fields__icon-library" class="uk-modal-container" data-uk-modal style="z-index: 9990;">
    <div class="uk-modal-dialog uk-height-1-1 uk-flex uk-flex-column">
        <button class="uk-modal-close-default" type="button" data-uk-close></button>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title"><?php _e('Icon Library', 'advanced-product');?></h3>
        </div>
        <div class="uk-modal-body uk-background-muted uk-overflow-hidden uk-height-1-1">
            <?php if($tabs = $this -> _get_tabs()){ ?>
                <div class="uk-height-1-1" data-uk-grid>
                    <div class="uk-width-auto">
                        <ul class="uk-tab-left" data-uk-tab="connect: #ap-field-icon-content;" id="ap-field-icon-nav">
                            <?php foreach($tabs as $tab){ ?>
                                <li><a href="#" data-ap-tab-item="<?php echo $tab['name'];?>"><?php echo $tab['label']; ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="uk-width-expand@m uk-flex uk-flex-column  uk-height-1-1">
                        <div class="uk-margin">
                            <div class="uk-search uk-search-default uk-width-expand uk-background-default">
                                <span data-uk-search-icon></span>
                                <input class="uk-search-input" type="search" placeholder="Search..." data-ap-search>
                            </div>
                        </div>
                        <div id="ap-field-icon-content" class="uk-overflow-auto uk-height-1-1 uk-text-center">
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button"><?php echo __('Cancel', 'advanced-product');?></button>
            <button class="uk-button uk-button-primary" type="button" data-ap-field-icon-insert><?php echo __('Insert', 'advanced-product');?></button>
        </div>
    </div>
</div>
