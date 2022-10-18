<?php

defined('ADVANCED_PRODUCT') or exit();

$fvalue     = isset($field['value'])?$field['value']:array();
$ficon      = isset($fvalue['icon'])?$fvalue['icon']:'';
$ficonType  = isset($fvalue['type'])?$fvalue['type']:'';
?>

<!--<div class="uk-card uk-card-default uk-card-body uk-card-hover uk-flex-inline uk-transition-toggle">-->
    <a href="javascript:" class="uk-card uk-card-default uk-card-body uk-card-hover uk-flex-inline uk-transition-toggle" data-ap-field-icon-modal>
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
        ?>
        <span data-uk-icon="icon: trash; ratio: 0.9" class="uk-position-top-right uk-transition-slide-top-small" style="margin: 7px 7px 0 0;z-index: 1;" data-ap-field-icon-delete></span>
        <?php
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
<!--</div>-->
