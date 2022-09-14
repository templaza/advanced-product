<?php

defined('ADVANCED_PRODUCT') or exit();

$fvalue     = isset($field['value'])?$field['value']:array();
$ficon      = isset($fvalue['icon'])?$fvalue['icon']:'';
$ficonType  = isset($fvalue['type'])?$fvalue['type']:'';
?>
<!--<a href="javascript:" class="uk-card uk-card-default uk-card-body uk-card-hover uk-flex-inline" data-uk-toggle="target: #ap-fields__icon-library">-->
<!--    --><?php //echo __('No Icon', 'advanced-product'); ?>
<!--</a>-->
<a href="javascript:" class="uk-card uk-card-default uk-card-body uk-card-hover uk-flex-inline" data-ap-field-icon-modal>
    <?php
    if(!empty($ficon)){
        if($ficonType == 'uikit-icon'){
     ?>
            <span data-uk-icon="icon:<?php echo $ficon; ?>;ratio:2;"></span>
    <?php
        }else{
            ?>
            <span class="<?php echo $ficon; ?> fa-3x"></span>
            <?php
        }
    }else {
        echo __('No Icon', 'advanced-product');
    }
    ?>
</a>
<input type="hidden" name="<?php echo $field['name']; ?>[icon]" data-ap-field-icon__icon value="<?php
    echo $ficon;
?>"/>
<input type="hidden" name="<?php echo $field['name']; ?>[type]" data-ap-field-icon__type value="<?php
echo $ficonType;
?>"/>

<!--<div id="ap-fields__icon-library" class="uk-modal-container" data-uk-modal>-->
<!--    <div class="uk-modal-dialog uk-height-1-1 uk-flex uk-flex-column">-->
<!--        <button class="uk-modal-close-default" type="button" data-uk-close></button>-->
<!--        <div class="uk-modal-header">-->
<!--            <h3 class="uk-modal-title">--><?php //_e('Icon Library', 'advanced-product');?><!--</h3>-->
<!--        </div>-->
<!--        <div class="uk-modal-body uk-background-muted uk-overflow-hidden uk-height-1-1">-->
<!---->
<!--            --><?php //if($tabs = $this -> _get_tabs()){ ?>
<!--                <div class="uk-height-1-1" data-uk-grid>-->
<!--                    <div class="uk-width-auto">-->
<!--                        <ul class="uk-tab-left" data-uk-tab="connect: #ap-field-icon-content;" id="ap-field-icon-nav">-->
<!--                            --><?php //foreach($tabs as $tab){ ?>
<!--                                <li><a href="#" data-ap-tab-item="--><?php //echo $tab['name'];?><!--">--><?php //echo $tab['label']; ?><!--</a></li>-->
<!--                            --><?php //} ?>
<!--                        </ul>-->
<!--                    </div>-->
<!--                    <div class="uk-width-expand@m uk-flex uk-flex-column  uk-height-1-1">-->
<!--                        <div class="uk-margin">-->
<!--                            <div class="uk-search uk-search-default uk-width-expand uk-background-default">-->
<!--                                <span data-uk-search-icon></span>-->
<!--                                <input class="uk-search-input" type="search" placeholder="Search..." data-ap-search>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div id="ap-field-icon-content" class="uk-overflow-auto uk-height-1-1 uk-text-center">-->
<!--                            <div data-uk-spinner></div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            --><?php //} ?>
<!--        </div>-->
<!--        <div class="uk-modal-footer uk-text-right">-->
<!--            <button class="uk-button uk-button-default uk-modal-close" type="button">--><?php //echo __('Cancel', 'advanced-product');?><!--</button>-->
<!--            <button class="uk-button uk-button-primary" type="button">--><?php //echo __('Insert', 'advanced-product');?><!--</button>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
