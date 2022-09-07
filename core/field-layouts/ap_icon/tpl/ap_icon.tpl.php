<?php

defined('ADVANCED_PRODUCT') or exit();
?>
<script type="text/template" id="tmpl-ap-template-field__ap-icon">
    <div class="uk-child-width-1-6 uk-padding uk-padding-remove-horizontal uk-padding-remove-top" data-uk-height-match="target: > div > .uk-card" data-uk-grid>
        <# _.each( data, function( tab, tab_key ) { #>
            <# _.each( tab.icons, function( icon, key ) { #>
            <# var displayPrefix = icon.displayPrefix,
                   prefix=icon.prefix,
                   icon_name = icon.selector,
                    icon_title = icon.name;
            #>
            <div data-ap-filter="{{{icon.filter}}}" data-ap-icon-type="{{{tab.name}}}">
                <a href="javascript:" data-uk-tooltip="{{{icon_title}}}"
                   class="uk-card uk-card-default uk-card-body uk-card-hover uk-flex uk-flex-column uk-text-center uk-flex-middle">
                    <# if (typeof tab.name !== "undefined" && tab.name == "uikit-icon"){ #>
                        <# if(icon.path !== undefined){ #>
                        {{{icon.path}}}
                        <# }else{ #>
                        <span {{{displayPrefix}}}="{{{icon_name}}}"></span>
                        <# } #>
                    <# }else{ #>
                        <span class="{{{displayPrefix}}} {{{icon_name}}} fa-2x"></span>
                    <# } #>
                    <span class="uk-text-truncate uk-margin-small-top">{{{icon_title}}}</span>
                </a>
            </div>
            <# }); #>
        <# }); #>
    </div>
</script>
