<?php

defined('ADVANCED_PRODUCT') or exit();

use Advanced_Product\AP_Functions;
use Advanced_Product\Helper\AP_Helper;
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Help', 'advanced-product');?></h1>
    <div class="advanced-product__wrap advanced-product__help-page" style="max-width: 1200px;">
        <div class="uk-card uk-card-body uk-card-default rounded-3 uk-border-rounded uk-margin-small-top">
            <h3 class="uk-h2"><?php _e('Advanced Product', 'advanced-product');?></h3>
            <p class="uk-text-meta uk-margin-remove-top"><?php
                echo sprintf(__('Version %s', 'advanced-product'), AP_Functions::get_my_version()); ?></p>
            <div class="uk-child-width-1-2@l" data-uk-grid>
                <div>
                    <ul class="uk-list">
                        <li>
                            <div class="">
                                <div class="setup-step-info">
                                    <dl class="uk-description-list uk-margin-remove-bottom">
                                        <?php
                                        $plData   = AP_Functions::get_my_data();
                                        ?>
                                        <?php if(!empty($plData) && isset($plData['Author'])){
                                            $authorUrl  = isset($plData['AuthorURI'])?$plData['AuthorURI']:'https://www.templaza.com/';
                                            ?>
                                            <dt><?php _e('Author:', 'advanced-product');?></dt>
                                            <dd><a class="uk-link-text" href="<?php echo $authorUrl; ?>"><?php echo $plData['Author'];?></a></dd>
                                        <?php } ?>
                                        <dt><?php _e('Forum Support:', 'advanced-product');?></dt>
                                        <dd><a class="uk-link-text" href="https://www.templaza.com/forums.html"><?php
                                                _e('Ask a question.', 'advanced-product');?></a></dd>
                                        <dt><?php _e('Plugin URI:', 'advanced-product');?></dt>
                                        <?php if(isset($plData['PluginURI'])){ ?>
                                            <dd><a class="uk-link-text" href="<?php echo $plData['PluginURI']; ?>"><?php
                                                    echo $plData['PluginURI']; ?></a></dd>
                                        <?php } ?>
                                        <dt><?php _e('Online Document:', 'advanced-product');?></dt>
                                        <dd><a class="uk-link-text" href="https://docs.templaza.com"><?php
                                                _e('Check Documentation', 'advanced-product');?></a></dd>
                                    </dl>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div>
                    <ul>
                        <li>
                            <div class="setup-step uk-padding-small uk-border-rounded">
                                <h3><?php _e('Install Data Sample', 'advanced-product');?></h3>
                                <p><?php _e('Click to install data sample', 'advanced-product'); ?></p>
                                <?php $disabled = AP_Helper::is_installed_sample_data()?' disabled':''; ?>
                                <button type="button" class="uk-button uk-button-default uk-border-pill"<?php
                                echo $disabled; ?> data-ap-install-sample-data>
                                    <span data-uk-spinner="ratio: 0.5" class="uk-margin-small-right uk-hidden ap-loading"></span><?php
                                    if($disabled){
                                        _e('Installed', 'advanced-product');
                                    }else {
                                        _e('Install', 'advanced-product');
                                    } ?>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="uk-child-width-1-3@l uk-margin-medium" data-uk-grid="">
            <div>
                <div class="uk-card uk-card-body uk-card-default uk-border-rounded">
                    <span data-uk-icon="icon: lifesaver; ratio: 3"></span>
                    <h3><?php _e('Help & Support', 'advanced-product');?></h3>
                    <p><?php _e('We would love to be of any assistance. Register an account, active your license and create a ticket', 'advanced-product');?></p>
                    <a class="uk-button uk-button-default uk-border-pill uk-margin-top" target="_blank" href="https://www.templaza.com/forums.html"><?php
                        _e('Ask a question', 'advanced-product');?></a>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-body uk-card-default uk-border-rounded">
                    <span data-uk-icon="icon: file-text; ratio: 3"></span>
                    <h3><?php _e('Documentation', 'advanced-product');?></h3>
                    <p><?php _e('Documentation, help files, and video tutorials for beginners and professionals','advanced-product');?></p>
                    <a class="uk-button uk-button-default uk-border-pill uk-margin-top" target="_blank" href="https://docs.templaza.com/advanced-products-plugin"><?php
                        _e('Read more', 'advanced-product');?></a>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-body uk-card-default uk-border-rounded">
                    <span data-uk-icon="icon: cog; ratio: 3"></span>
                    <h3><?php _e('System Requirements', 'advanced-product');?></h3>
                    <p><?php echo sprintf(__('Requirements for %s: WordPress Version, Web Server, Database, Browser, etc', 'advanced-product'),
                        $plData['Name']);?></p>
                    <a class="uk-button uk-button-default uk-border-pill uk-margin-top" target="_blank" href="https://docs.templaza.com/guides/system-requirements"><?php
                        _e('Read more', 'advanced-product');?></a>
                </div>
            </div>
        </div>
    </div>
</div>
