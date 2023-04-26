(function($, window){
    "use strict";
    $(function () {

        var ap_custom_field_init = function(){
            if(typeof acf === "undefined"){
                return;
            }
            if(typeof acf !== "undefined" && typeof acf.helpers.create_field === "undefined"){
                /*
                *  Create Field
                *
                *  @description:
                *  @since 3.5.1
                *  @created: 11/10/12
                */

                acf.helpers.create_field = function( options ){

                    // dafaults
                    var defaults = {
                        'type' 		: 'text',
                        'classname'	: '',
                        'name' 		: '',
                        'value' 	: ''
                    };
                    options = $.extend(true, defaults, options);


                    // vars
                    var html = "";

                    if( options.type == "text" )
                    {
                        html += '<input class="text ' + options.classname + '" type="text" id="' + options.name + '" name="' + options.name + '" value="' + options.value + '" />';
                    }
                    else if( options.type == "select" )
                    {
                        // vars
                        var groups = {};


                        // populate groups
                        $.each(options.choices, function(k, v){

                            // group may not exist
                            if( v.group === undefined )
                            {
                                v.group = 0;
                            }


                            // instantiate group
                            if( groups[ v.group ] === undefined )
                            {
                                groups[ v.group ] = [];
                            }


                            // add to group
                            groups[ v.group ].push( v );

                        });


                        html += '<select class="select ' + options.classname + '" id="' + options.name + '" name="' + options.name + '">';

                        $.each(groups, function(k, v){

                            // start optgroup?
                            if( k != 0 )
                            {
                                html += '<optgroup label="' + k + '">';
                            }


                            // options
                            $.each(v, function(k2, v2){

                                var attr = '';

                                if( v2.value == options.value )
                                {
                                    attr = 'selected="selected"';
                                }

                                html += '<option ' + attr + ' value="' + v2.value + '">' + v2.label + '</option>';

                            });


                            // end optgroup?
                            if( k != 0 )
                            {
                                html += '</optgroup>';
                            }

                        });


                        html += '</select>';
                    }

                    html = $(html);

                    return html;

                };
            }
            var $field  = $("#ap_meta_box_field_type");
            // vars
            var choices		= [],
                key			= $field.attr('data-id'),
                $ancestors	= $field.parents('.fields'),
                $tr			= $field.find('.field_form tr.conditional-logic');


            $.each( $ancestors, function( i ){

                var group = (i == 0) ? acf.l10n.sibling_fields : acf.l10n.parent_fields;

                $(this).children('.field').each(function(){


                    // vars
                    var $this_field	= $(this),
                        this_id		= $this_field.attr('data-id'),
                        this_type	= $this_field.attr('data-type'),
                        this_label	= $this_field.find('tr.field_label input').val();


                    // validate
                    if( this_id == 'field_clone' )
                    {
                        return;
                    }

                    if( this_id == key )
                    {
                        return;
                    }


                    // add this field to available triggers
                    if( this_type == 'select' || this_type == 'checkbox' || this_type == 'true_false' || this_type == 'radio' )
                    {
                        choices.push({
                            value	: this_id,
                            label	: this_label,
                            group	: group
                        });
                    }


                });

            });
            // empty?
            if( choices.length == 0 )
            {
                choices.push({
                    'value' : 'null',
                    'label' : acf.l10n.no_fields
                });
            }

            // create select fields
            $tr.find('.conditional-logic-field').each(function(){

                if($(this).hasClass("select")) {

                    // trigger change
                    $(this).trigger("change");

                    return true;
                }

                var val = $(this).val(),
                    name = $(this).attr('name');


                // create select
                var $select = acf.helpers.create_field({
                    'type': 'select',
                    'classname': 'conditional-logic-field',
                    'name': name,
                    'value': val,
                    'choices': choices
                });


                // update select
                $(this).replaceWith($select);

                // trigger change
                $select.trigger('change');

            });
        };

        if(window.pagenow == "ap_custom_field") {
            var ap_field_load_option = function ($el, $options) {
                // vars
                var select = $el,
                    tbody = select.closest('tbody'),
                    field = tbody.closest('.field'),
                    field_type = field.attr('data-type'),
                    field_key = field.attr('data-id'),
                    val = select.val();

                // update data atts
                field.removeClass('field_type-' + field_type).addClass('field_type-' + val);
                field.attr('data-type', val);

                // tab - override field_name
                if (val == 'tab' || val == 'message') {
                    tbody.find('tr.field_name input[type="text"]').val('').trigger('keyup');
                }

                // show field options if they already exist
                if (tbody.children('tr.field_option_' + val).exists()) {
                    // hide + disable options
                    tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');

                    // show and enable options
                    tbody.children('tr.field_option_' + val).show().find('[name]').removeAttr('disabled');
                } else {
                    // add loading gif
                    var tr = $('<tr><td class="label"></td><td><div class="acf-loading"></div></td></tr>');

                    // hide current options
                    tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');


                    // append tr
                    if (tbody.children('tr.conditional-logic').exists()) {
                        tbody.children('tr.conditional-logic').before(tr);
                    } else {
                        tbody.children('tr.field_save').before(tr);
                    }


                    var ajax_data = {
                        'action': 'acf/field_group/render_options',
                        // 'action' : 'advanced-product/field_group/render_options',
                        'post_id': acf.post_id,
                        'field_key': select.attr('name'),
                        'field_type': val,
                        'nonce': acf.nonce
                    };

                    ajax_data = Object.assign(ajax_data, $options);

                    $.ajax({
                        url: ajaxurl,
                        data: ajax_data,
                        type: 'post',
                        dataType: 'html',
                        success: function (html) {

                            if (!html) {
                                tr.remove();
                                return;
                            }

                            tr.replaceWith(html);

                            $el.trigger("ajaxCompleted", html);
                            // $el.triggerHandler("ajaxCompleted", tr);
                        }
                    });
                }
            };

            $(document).on('keyup', '#ap_meta_box_field_type tr.field_label input', function (e) {
                var __main = $(this).closest("#poststuff");
                __main.find("input[name=post_title]").prev("#title-prompt-text").prop("class", "screen-reader-text")
                    .end().val($(this).val());
            });
            $(document).on('keyup', 'body.post-type-ap_custom_field input#title[name=post_title]', function(e){
                var __main  = $(this).closest("#poststuff");
                __main.find("#ap_meta_box_field_type tr.field_label input").val($(this).val());
            });
            $(document).on('blur', '#ap_meta_box_field_type tr.field_label input,body.post-type-ap_custom_field input[name=post_title]', function(e){
                var __main  = $(this).closest("#poststuff");

                if(!__main.find("#ap_meta_box_field_type tr.field_name input").val().trim().length) {
                    __main.find("#ap_meta_box_field_type tr.field_name input").val(wpFeSanitizeTitle($(this).val()));
                }
            });
            $(document).on('change', '#ap_meta_box_field_type tr.field_search_type select', function (e) {
                // __ap_field_load_option($(this), {
                //     "action": "advanced-product/field_layouts/render_options",
                // });


                // vars
                var select = $(this),
                    tbody = select.closest('tbody'),
                    field = tbody.closest('.field'),
                    field_type = field.attr('data-type'),
                    field_key = field.attr('data-id'),
                    val = select.val(),
                    f_type = $("#ap_meta_box_field_type tr.field_type select, #ap_meta_box_field_type tr.field_type input").val();

                // update data atts
                field.removeClass('field_type-' + field_type).addClass('field_type-' + val);
                field.attr('data-type', val);

                // // tab - override field_name
                // if( val == 'tab' || val == 'message' )
                // {
                //     tbody.find('tr.field_name input[type="text"]').val('').trigger('keyup');
                // }

                // show field options if they already exist
                if (tbody.children('tr.field_search_option.field_option_' + f_type + '.field_search_option_' + val).exists()) {
                    // hide + disable options
                    tbody.children('tr.field_search_option.field_option_' + f_type).hide().find('[name]').attr('disabled', 'true');

                    // show and enable options
                    tbody.children('tr.field_search_option.field_option_' + f_type + '.field_search_option_' + val).show().find('[name]').removeAttr('disabled');

                    select.trigger("changed");
                } else {
                    // add loading gif
                    var tr = $('<tr><td class="label"></td><td><div class="acf-loading"></div></td></tr>');

                    // hide current options
                    tbody.children('tr.field_search_option.field_option_' + f_type).hide().find('[name]').attr('disabled', 'true');

                    // // hide current options
                    // tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');

                    // // hide current options
                    // tbody.children('tr.field_search_option').hide().find('[name]').attr('disabled', 'true');

                    // append tr
                    if (tbody.children('tr.conditional-logic').exists()) {
                        tbody.children('tr.conditional-logic').before(tr);
                    } else {
                        tbody.children('tr.field_save').before(tr);
                    }

                    var ajax_data = {
                        'action': 'advanced-product/field_layouts/render_search_options',
                        'post_id': acf.post_id,
                        // 'field_key' : select.attr('name'),
                        // 'field_type' : val,
                        'field_key': select.attr('name'),
                        'field_type': $("tr.field_type select, tr.field_type input").val(),
                        'field_search_type': val,
                        'nonce': acf.nonce
                    };

                    // ajax_data   = Object.assign(ajax_data, $options);

                    $.ajax({
                        url: ajaxurl,
                        data: ajax_data,
                        type: 'post',
                        dataType: 'html',
                        success: function (html) {

                            if (!html) {
                                tr.remove();
                                return;
                            }

                            tr.replaceWith(html);

                            // $el.trigger("ajaxCompleted", html);
                            // $el.triggerHandler("ajaxCompleted", tr);

                            select.trigger("ajaxCompleted", html, tr);
                        }
                    });
                }
            });

            $(document).on('change', '#ap_meta_box_field_type tr.field_type select', function () {
                ap_field_load_option($(this));
            });

            $(document).on('change', '#ap_meta_box_field_type tr.conditional-logic input[type="radio"]', function (e) {

                e.preventDefault();

                ap_custom_field_change_toggle($(this));

            });


            $(document).on('click', '#ap_meta_box_field_type tr.conditional-logic .acf-button-add', function (e) {

                e.preventDefault();

                ap_custom_field_add($(this).closest('tr'));

            });

            $(document).on('click', '#ap_meta_box_field_type tr.conditional-logic .acf-button-remove', function (e) {

                e.preventDefault();

                ap_custom_field_remove($(this).closest('tr'));

            });

            $(document).on('change', '#ap_meta_box_field_type select.conditional-logic-field', function (e) {

                e.preventDefault();

                ap_custom_field_change_trigger($(this));

            });

            var ap_custom_field_change_toggle = function ($input) {

                // vars
                var val = $input.val(),
                    $tr = $input.closest('tr.conditional-logic');


                if (val == "1") {
                    $tr.find('.contional-logic-rules-wrapper').show();

                    $tr.find("select.conditional-logic-field").trigger("change");
                } else {
                    $tr.find('.contional-logic-rules-wrapper').hide();
                }

            };

            var ap_custom_field_remove = function ($tr) {

                var $table = $tr.closest('table');

                // validate
                if ($table.hasClass('remove-disabled')) {
                    return false;
                }


                // remove tr
                $tr.remove();


                // add clas to table
                if ($table.find('tr').length <= 1) {
                    $table.addClass('remove-disabled');
                }

            };

            var ap_custom_field_add = function ($old_tr) {

                // vars
                var $new_tr = $old_tr.clone(),
                    old_i = parseFloat($old_tr.attr('data-i')),
                    new_i = acf.helpers.uniqid();


                // console.log($new_tr.find('[name]'));
                // update names
                $new_tr.find('[name]').each(function () {

                    // flexible content uses [0], [1] as the layout index. To avoid conflict, make sure we search for the entire conditional logic string in the name and id
                    var find = '[conditional_logic][rules][' + old_i + ']',
                        replace = '[conditional_logic][rules][' + new_i + ']';

                    $(this).attr('name', $(this).attr('name').replace(find, replace));
                    $(this).attr('id', $(this).attr('id').replace(find, replace));

                });


                // update data-i
                $new_tr.attr('data-i', new_i);


                // add tr
                $old_tr.after($new_tr);


                // remove disabled
                $old_tr.closest('table').removeClass('remove-disabled');

            };

            var ap_custom_field_change_trigger = function ($select) {

                // vars
                var val = $select.val(),
                    $trigger = $('.field_key-' + val),
                    type = $trigger.attr('data-type'),
                    $value = $select.closest('tr').find('.conditional-logic-value'),
                    choices = [];

                // populate choices
                if (type == "true_false") {
                    choices = [
                        {value: 1, label: acf.l10n.checked}
                    ];

                } else if (type == "select" || type == "checkbox" || type == "radio") {
                    var field_choices = $trigger.find('.field_option-choices').val().split("\n");

                    if (field_choices) {
                        for (var i = 0; i < field_choices.length; i++) {
                            var choice = field_choices[i].split(':');

                            var label = choice[0];
                            if (choice[1]) {
                                label = choice[1];
                            }

                            choices.push({
                                'value': $.trim(choice[0]),
                                'label': $.trim(label)
                            });

                        }
                    }

                } else if ($select.hasClass("select")) {
                    choices = false;

                    var __tr = $select.closest("tr");

                    $value.prop("disabled", true).hide();
                    __tr.find(".conditional-logic-value-" + $select.val()).prop("disabled", false).show();
                }

                if (choices !== false) {
                    // create select
                    var $select = acf.helpers.create_field({
                        'type': 'select',
                        'classname': 'conditional-logic-value',
                        'name': $value.attr('name'),
                        'value': $value.val(),
                        'choices': choices
                    });

                    $value.replaceWith($select);

                    $select.trigger('change');
                }

            };

            ap_custom_field_init();
        }

        if(window.pagenow == 'ap_product'){
            /* Function to enable or disable taxonomy associated to other taxonomy */
            if($("form#post [data-field_type=taxonomy]").length) {
                /* Enable or disable taxonomy */
                var _ap_enable_disable_options = function($f_name, $f_value){
                    if(!$f_value || !$f_value.length){
                        $("form#post [data-field_type=taxonomy] [data-associate-from=" + $f_name+"]").prop("disabled", true);
                        return;
                    }

                    var __ap_set_enable_disable_option = function($_f_value, $_disabled = true){

                        var __f_option = $("form#post [data-field_type=taxonomy] [data-associate-from=" + $f_name+"][data-associate~=" + $_f_value+"]");

                        var __main  = $("form#post [data-field_type=taxonomy] [data-associate-from=" + $f_name+"]").closest(".field_type-taxonomy");

                        // $_disabled  = typeof
                        // __main.find("[data-associate-from="+ $f_name+"]").prop("disabled", true);
                        // console.log("__main");
                        // console.log(__main);
                        // console.log("$_disabled");
                        // console.log($_disabled);
                        if(__f_option.length) {
                            __main.find("[data-associate-from=" + $f_name + "]:not([data-associate~="
                                + $_f_value + "])").prop("disabled", true);
                            __f_option.prop("disabled", false);
                        }else{
                            __main.find("[data-associate-from="+ $f_name+"]").prop("disabled", true);
                            if(__main.find("select").length){
                                __main.find("select").val("");
                            }
                        }
                    };

                    // console.log("$f_value");
                    // console.log($f_value);
                    if(typeof $f_value === "object"){
                        var __main  = $("form#post [data-field_type=taxonomy] [data-associate-from=" + $f_name+"]").closest(".field_type-taxonomy");
                        __main.find("[data-associate-from="+ $f_name+"]").prop("disabled", true);

                        $.each($f_value, function(index, f_val){
                            __ap_set_enable_disable_option(f_val, false);
                        });

                    }else{
                        __ap_set_enable_disable_option($f_value);

                        // var __f_option = $("form#post [data-field_type=taxonomy] [data-associate-from=" + $f_name+"][data-associate~=" + $f_value+"]");
                        //
                        // if(__f_option.length) {
                        //     __f_option.parent().find("> [data-associate-from=" + $f_name + "]:not([data-associate~="
                        //         + $f_value + "])").prop("disabled", true);
                        //     __f_option.prop("disabled", false);
                        // }else{
                        //     $("form#post [data-field_type=taxonomy] [data-associate-from="
                        //         + $f_name+"]").prop("disabled", true)
                        //         .parent().val("");
                        // }
                    }
                };
                $("form#post [data-field_type=taxonomy]").each(function () {

                    var __f = $(this),
                        __f_name = $(this).attr("data-field_name"),
                        __f_key = $(this).attr("data-field_key"),
                        __f_control = $(this).find("#acf-field-"+__f_name);

                    if(!__f_control.length) {
                        __f_control = $(this).find("[name^=fields\\\[" + __f_key + "\\\]]");
                        // $.each(__f_control, function(){
                        //     if(typeof $(this).prop("checked") !== "undefined" && $(this).prop("checked")) {
                        //         _ap_enable_disable_options(__f_name, $(this).val());
                        //     }
                        // });
                    }
                    // else{
                    //     _ap_enable_disable_options(__f_name, __f_control.val());
                    // }

                    // var __main  = $("form#post [data-field_type=taxonomy] [data-associate-from=" + __f_name+"]").closest(".field_type-taxonomy");
                    //
                    // if(__main.length) {
                    //     __main.find("[data-associate-from="+ __f_name+"]").prop("disabled", true);
                    // }

                    if(__f_control.length) {
                        $.each(__f_control, function () {
                            var __f_value   = typeof $(this).val() !== "undefined"?$(this).val():"";
                            if (typeof $(this).prop("checked") !== "undefined" && !$(this).prop("checked")) {
                                __f_value   = "";
                            }
                            // if (typeof $(this).prop("checked") === "undefined" || $(this).prop("checked")) {
                                // _ap_enable_disable_options(__f_name, $(this).val());
                            // }
                            _ap_enable_disable_options(__f_name, __f_value);
                        });
                    }

                    __f_control.on("change", function() {
                        var __f_value = $(this).val();

                        if (!__f_value.length) {
                            return;
                        }

                        if (typeof $(this).prop("checked") !== "undefined" && !$(this).prop("checked")) {
                            __f_value   = "";
                        }
                        // console.log(__f_value);
                        // if (typeof $(this).prop("checked") === "undefined" || $(this).prop("checked")) {
                            _ap_enable_disable_options(__f_name, __f_value);
                        // }
                        // else{
                        //     $("form#post [data-field_type=taxonomy] [data-associate-from=" + __f_name+"]").prop("disabled", true);
                        // }
                    });

                });
            }

            var ap_product_init = function() {
                /* Ajax function to get metabox with acf fields */
                var _ap_product_ajax = function($obj, $branch_slug){
                    if(typeof $obj === "undefined" || !$obj.length){
                        return;
                    }

                    $branch_slug    = typeof $branch_slug !== "undefined"?$branch_slug:$obj.val();

                    if(typeof $branch_slug === "undefined" || !$branch_slug){
                        return;
                    }

                    var __ajax_loaded = false,
                        __ajax_data =  {
                        "action": "load_custom_fields",
                        "branch_slug": $branch_slug,
                        "post_type": window.typenow,
                    };
                    if(typeof ap_product.post_id !== "undefined") {
                        __ajax_data.post_id = ap_product.post_id;
                    }
                    if(typeof ap_product.custom_fields.nonce !== "undefined") {
                        __ajax_data.nonce = ap_product.custom_fields.nonce;
                    }

                    $obj.data("__ajax_loaded", __ajax_loaded);
                    if(!__ajax_loaded) {

                        // var _main   = $("#postbox-container-2").find(".acf_postbox:last");
                        // console.log(_main);
                        // console.log($("#postbox-container-2").find(".acf_postbox:last"));

                        /* Insert loading after branch option */
                        if(!$("#postbox-container-2 > .acf-loading:first").length) {
                            $("#postbox-container-2").prepend("<div class=\"acf-loading\"/>");
                        }
                        if(typeof $obj !== "undefined" && !$obj.next(".acf-loading").length) {
                            $obj.after("<div class=\"acf-loading\"/>");
                        }

                        $.post(window.ajaxurl, __ajax_data, function (response) {
                            var _html = response.data,
                                _main_id = "#" + $(_html).attr("id");

                            $(_main_id + "> .ap-acf_postbox-ajax").remove();
                            // $(_main_id + "> .acf_postbox").addClass("acf-hidden");


                            var __postbox   = $(_html).find(".postbox");

                            __postbox.addClass("ap-acf_postbox-ajax");
                            __postbox.find(".hndle,.handlediv").on('click.postboxes', window.postboxes.handle_click);

                            // Handle the order of the postboxes.
                            __postbox.find(".handle-order-higher, .handle-order-lower").on('click.postboxes', window.postboxes.handleOrder);

                            if($(_main_id + "> .postbox.acf_postbox:not(.ap-acf_postbox-ajax)").length){
                                $(_main_id + "> .postbox.acf_postbox:not(.ap-acf_postbox-ajax):last").after(__postbox);
                            }else {
                                $(_main_id).prepend(__postbox);
                            }

                            // Reinitialize the editor: Remove the editor then add it back
                            if($(_main_id).find(".acf_wysiwyg textarea").length) {
                                $(_main_id).find(".acf_wysiwyg textarea").each(function () {
                                    var __textarea_id = $(this).attr("id");

                                    // Reinitialize the editor: Remove the editor then add it back
                                    tinymce.execCommand('mceRemoveEditor', false, __textarea_id);
                                    tinymce.execCommand('mceAddEditor', false, __textarea_id);
                                });
                            }

                            $(_main_id).find('.acf-date_picker').each(function(){

                                acf.fields.date_picker.set({ $el : $(this) }).init();

                            });

                            $(_main_id).sortable("refresh");

                            if(typeof $obj !== "undefined" && $obj.next(".acf-loading").length){
                                $obj.next(".acf-loading").remove();
                            }
                            if($("#postbox-container-2 > .acf-loading:first-child").length){
                                $("#postbox-container-2 > .acf-loading:first-child").remove();
                            }

                            __ajax_loaded   = true;

                            // $obj.data("__ajax_loaded", $branch_slug);
                            $obj.data("__ajax_loaded", __ajax_loaded);

                            // window.postboxes.add_postbox_toggles(window.pagenow);
                        }, 'json');
                    }
                };

                // Disable trigger valid postbox group of acf
                $(document).off("acf/update_field_groups");
                // $(document).on("acf/update_field_groups", function(e){
                //     // e.preventDefault();
                //     e.stopPropagation();
                // });
                // $("#acf-field-ap_branch").on("change", function (e) {

                $(document).on("change","#acf-field-ap_branch", function (e) {
                    e.preventDefault();

                    // var __ajax_loaded   = $(this).data("__ajax_loaded");
                    //
                    // if(typeof __ajax_loaded !== "undefined" && !__ajax_loaded){
                    //     return;
                    // }

                    _ap_product_ajax($(this));
                    // _ap_product_ajax($(this).val(), $(this));
                });

                /* Init ajax when page load */
                // _ap_product_ajax($("#acf-field-ap_branch").val());
                _ap_product_ajax($("#acf-field-ap_branch"));
            };
            ap_product_init();
        }

        if(window.pagenow == "ap_custom_category" && (window.adminpage == "post-php" || window.adminpage == "post-new-php")){
            /* Custom Category post type */
            $("#acf_acf_subcategory_general").on("change keyup", "#acf-field-singular_name", function(){
                var __main  = $(this).closest("#acf_acf_subcategory_general");
                if(__main.find("#acf-field-slug").length) {
                    __main.find("#acf-field-slug").val(wpFeSanitizeTitle($(this).val()));
                }
                // var __main = $(this).closest("#poststuff");
                if(!__main.closest("#poststuff").find("input[name=post_title]").val().length) {
                    __main.closest("#poststuff").find("input[name=post_title]")
                        .prev("#title-prompt-text").prop("class", "screen-reader-text")
                        .end().val($(this).val());
                }
            });

            $(document).on('blur', 'body.post-type-ap_custom_category input[name=post_title]', function(e){
                var __main  = $(this).closest("#poststuff");

                if(!__main.find("input#acf-field-singular_name").val().trim().length) {
                    __main.find("input#acf-field-singular_name").val($(this).val());
                }
                if(__main.find("#acf-field-slug").length) {
                    __main.find("#acf-field-slug").val(wpFeSanitizeTitle($(this).val()));
                }
                // if(!__main.find("#ap_meta_box_field_type tr.field_name input").val().trim().length) {
                //     __main.find("#ap_meta_box_field_type tr.field_name input").val(wpFeSanitizeTitle($(this).val()));
                // }
            });
        //     $(document).on("keyup", "input#acf-field-singular_name", function(){
        //         var __main = $(this).closest("#poststuff");
        //         __main.find("input[name=post_title]").prev("#title-prompt-text").prop("class", "screen-reader-text")
        //             .end().val($(this).val());
        //     });
        }

        if( window.typenow === "ap_custom_field" && (window.adminpage === "edit-php"
            || (window.adminpage === "edit-tags-php" && window.pagenow === "edit-ap_group_field"))) {
            $(document).ready(function () {

                var getUrlParameter = function getUrlParameter(sParam)
                {
                    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                        sURLVariables = sPageURL.split('&'),
                        sParameterName,
                        i;

                    for (i = 0; i < sURLVariables.length; i++) {
                        sParameterName = sURLVariables[i].split('=');

                        if (sParameterName[0] === sParam) {
                            return sParameterName[1] === undefined ? true : sParameterName[1];
                        }
                    }
                };

                if(typeof advanced_product.orderby !== "undefined") {

                    if(advanced_product.orderby === "menu_order" || advanced_product.orderby === "term_order"){
                        $("body.post-type-ap_custom_field table.wp-list-table tbody").sortable({
                            axis: "y",
                            items: 'tr',
                            containment: "parent",
                            // cursor: 'move',
                            handle: '.ap-handle',
                            helper: function (e, ui) {
                                //hard set left position to fix y-axis drag problem on Safari
                                $(ui).css({'left': '0px'});

                                ui.children().each(function () {
                                    $(this).width($(this).width());
                                });
                                // $(ui).children('td').addClass('dndlist-dragged-row');
                                return ui;
                            },
                            placeholder: {
                                element: function (currentItem) {
                                    // var cols    =   $(currentItem).children('td:visible').length + 1;
                                    var cols = $(currentItem).children('td').length + 1;
                                    return $('<tr class="ui-sortable-placeholder"><td colspan="' + cols + '">&nbsp;</td></tr>')[0];
                                },
                                update: function (container, p) {
                                    return;
                                }
                            },
                            update: function (event, ui) {
                                var order = $('#the-list').sortable('serialize');
                                var paged = getUrlParameter('paged');
                                if (typeof paged === 'undefined')
                                    paged = 1;

                                var queryString = {
                                    "action": "ap_post_type_" + window.typenow + "_archive_sortable",
                                    "post_type": window.typenow, "order": order, "paged": paged,
                                    "archive_sort_nonce": advanced_product.archive_sort_nonce
                                };
                                if(advanced_product.orderby === "term_order"){
                                    queryString["action"] = "ap_taxonomy_" + getUrlParameter("taxonomy") + "_sortable";
                                }
                                $.ajax({
                                    type: 'POST',
                                    url: window.ajaxurl,
                                    data: queryString,
                                    cache: false,
                                    dataType: "html",
                                    success: function (data) {

                                    },
                                    error: function (html) {

                                    }
                                });
                            },
                        });
                    }
                    // if(advanced_product.orderby === "term_order"){
                    //     $("body.post-type-ap_custom_field table.wp-list-table tbody").sortable({
                    //         axis: "y",
                    //         items: 'tr',
                    //         containment: "parent",
                    //         // cursor: 'move',
                    //         handle: '.ap-handle',
                    //         helper: function (e, ui) {
                    //             //hard set left position to fix y-axis drag problem on Safari
                    //             $(ui).css({'left': '0px'});
                    //
                    //             ui.children().each(function () {
                    //                 $(this).width($(this).width());
                    //             });
                    //             // $(ui).children('td').addClass('dndlist-dragged-row');
                    //             return ui;
                    //         },
                    //         placeholder: {
                    //             element: function (currentItem) {
                    //                 // var cols    =   $(currentItem).children('td:visible').length + 1;
                    //                 var cols = $(currentItem).children('td').length + 1;
                    //                 return $('<tr class="ui-sortable-placeholder"><td colspan="' + cols + '">&nbsp;</td></tr>')[0];
                    //             },
                    //             update: function (container, p) {
                    //                 return;
                    //             }
                    //         },
                    //         update: function (event, ui) {
                    //             var order = $('#the-list').sortable('serialize');
                    //             var paged = getUrlParameter('paged');
                    //             if (typeof paged === 'undefined')
                    //                 paged = 1;
                    //
                    //             var queryString = {
                    //                 "action": "ap_post_type_" + window.typenow + "_archive_sortable",
                    //                 "post_type": window.typenow, "order": order, "paged": paged,
                    //                 "archive_sort_nonce": advanced_product.archive_sort_nonce
                    //             };
                    //             $.ajax({
                    //                 type: 'POST',
                    //                 url: window.ajaxurl,
                    //                 data: queryString,
                    //                 cache: false,
                    //                 dataType: "html",
                    //                 success: function (data) {
                    //
                    //                 },
                    //                 error: function (html) {
                    //
                    //                 }
                    //             });
                    //         },
                    //     });
                    // }
                }
            });
        }
    });

    // $(document).ready(function(){
    //     console.log(acf);
    //     console.log($('tr.conditional-logic input[type="radio"]'));
    //     acf.conditional_logic.init();
    // });
})(jQuery, window);