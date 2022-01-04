(function($){
    "use strict";
    $(function () {

        /* Custom Category post type */
        $("#acf_acf_subcategory_general").on("change keyup", "#acf-field-singular_name", function(){
            var __main  = $(this).closest("#acf_acf_subcategory_general");
            if(__main.find("#acf-field-slug").length) {
                __main.find("#acf-field-slug").val(wpFeSanitizeTitle($(this).val()));
            }
        });

        /* Taxonomy */
        if($("form#post [data-field_type=taxonomy]").length) {
            $("form#post [data-field_type=taxonomy]").each(function () {
                var __f = $(this),
                    __f_name = $(this).attr("data-field_name"),
                    __f_control = $(this).find("#acf-field-"+__f_name);
                __f_control.on("change", function(){
                    var __f_value = $(this).val();

                    if(!__f_value.length){
                        return;
                    }
                    var __f_option = $("form#post [data-field_type=taxonomy] [data-associate-from=" + __f_name+"][data-associate~=" + __f_value+"]");

                    if(__f_option.length) {
                        __f_option.parent().find("> [data-associate-from=" + __f_name + "]:not([data-associate~=" + __f_value + "])").prop("disabled", true);
                        __f_option.prop("disabled", false);
                    }
                });


            });
        }

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

        var ap_field_load_option  = function($el, $options){
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
            if( val == 'tab' || val == 'message' )
            {
                tbody.find('tr.field_name input[type="text"]').val('').trigger('keyup');
            }

            // show field options if they already exist
            if( tbody.children( 'tr.field_option_' + val ).exists() )
            {
                // hide + disable options
                tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');

                // show and enable options
                tbody.children( 'tr.field_option_' + val ).show().find('[name]').removeAttr('disabled');
            }
            else
            {
                // add loading gif
                var tr = $('<tr"><td class="label"></td><td><div class="acf-loading"></div></td></tr>');

                // hide current options
                tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');


                // append tr
                if( tbody.children('tr.conditional-logic').exists() )
                {
                    tbody.children('tr.conditional-logic').before(tr);
                }
                else
                {
                    tbody.children('tr.field_save').before(tr);
                }


                var ajax_data = {
                    'action' : 'acf/field_group/render_options',
                    // 'action' : 'advanced-product/field_group/render_options',
                    'post_id' : acf.post_id,
                    'field_key' : select.attr('name'),
                    'field_type' : val,
                    'nonce' : acf.nonce
                };

                ajax_data   = Object.assign(ajax_data, $options);

                $.ajax({
                    url: ajaxurl,
                    data: ajax_data,
                    type: 'post',
                    dataType: 'html',
                    success: function(html){

                        if( ! html )
                        {
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

        $(document).on('keyup', '#ap_meta_box_field_type tr.field_label input', function(e){
            var __main  = $(this).closest("#poststuff");
            __main.find("input[name=post_title]").prev("#title-prompt-text").prop("class", "screen-reader-text")
                .end().val($(this).val());
        });
        $(document).on('change', '#ap_meta_box_field_type tr.field_search_type select', function(e){
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
            if( tbody.children( 'tr.field_search_option.field_option_' + f_type +'.field_search_option_' + val ).exists() )
            {
                // hide + disable options
                tbody.children('tr.field_search_option.field_option_' + f_type).hide().find('[name]').attr('disabled', 'true');

                // show and enable options
                tbody.children( 'tr.field_search_option.field_option_' + f_type + '.field_search_option_' + val ).show().find('[name]').removeAttr('disabled');

                select.trigger("changed");
            }
            else
            {
                // add loading gif
                var tr = $('<tr"><td class="label"></td><td><div class="acf-loading"></div></td></tr>');


                // hide current options
                tbody.children('tr.field_search_option.field_option_'+f_type).hide().find('[name]').attr('disabled', 'true');

                // // hide current options
                // tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');

                // // hide current options
                // tbody.children('tr.field_search_option').hide().find('[name]').attr('disabled', 'true');


                // append tr
                if( tbody.children('tr.conditional-logic').exists() )
                {
                    tbody.children('tr.conditional-logic').before(tr);
                }
                else
                {
                    tbody.children('tr.field_save').before(tr);
                }

                var ajax_data = {
                    'action' : 'advanced-product/field_layouts/render_search_options',
                    'post_id' : acf.post_id,
                    // 'field_key' : select.attr('name'),
                    // 'field_type' : val,
                    'field_key' : select.attr('name'),
                    'field_type' : $("tr.field_type select, tr.field_type input").val(),
                    'field_search_type' : val,
                    'nonce' : acf.nonce
                };

                // ajax_data   = Object.assign(ajax_data, $options);

                $.ajax({
                    url: ajaxurl,
                    data: ajax_data,
                    type: 'post',
                    dataType: 'html',
                    success: function(html){

                        if( ! html )
                        {
                            tr.remove();
                            return;
                        }

                        tr.replaceWith(html);

                        // $el.trigger("ajaxCompleted", html);
                        // $el.triggerHandler("ajaxCompleted", tr);

                        console.log(select);
                        select.trigger("ajaxCompleted", html, tr);
                    }
                });
            }
        });

        // $(document).on('ajaxCompleted', '#ap_meta_box_field_type tr.field_type select', function(e, html){
        //     console.log($(html).find("tr.field_search_type"));
        //     // console.log($("tr.field_search_type select").trigger("change"));
        // });
        $(document).on('change', '#ap_meta_box_field_type tr.field_type select', function(){
            ap_field_load_option($(this));

            // $(this).on("ajaxCompleted", function(){
            //     alert("ajaxCompleted");
            // });

            // // vars
            // var select = $(this),
            //     tbody = select.closest('tbody'),
            //     field = tbody.closest('.field'),
            //     field_type = field.attr('data-type'),
            //     field_key = field.attr('data-id'),
            //     val = select.val();
            //
            //
            //
            // // update data atts
            // field.removeClass('field_type-' + field_type).addClass('field_type-' + val);
            // field.attr('data-type', val);
            //
            //
            // // tab - override field_name
            // if( val == 'tab' || val == 'message' )
            // {
            //     tbody.find('tr.field_name input[type="text"]').val('').trigger('keyup');
            // }
            //
            //
            // // show field options if they already exist
            // if( tbody.children( 'tr.field_option_' + val ).exists() )
            // {
            //     // hide + disable options
            //     tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');
            //
            //     // show and enable options
            //     tbody.children( 'tr.field_option_' + val ).show().find('[name]').removeAttr('disabled');
            // }
            // else
            // {
            //     // add loading gif
            //     var tr = $('<tr"><td class="label"></td><td><div class="acf-loading"></div></td></tr>');
            //
            //     // hide current options
            //     tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');
            //
            //
            //     // append tr
            //     if( tbody.children('tr.conditional-logic').exists() )
            //     {
            //         tbody.children('tr.conditional-logic').before(tr);
            //     }
            //     else
            //     {
            //         tbody.children('tr.field_save').before(tr);
            //     }
            //
            //
            //     var ajax_data = {
            //         'action' : 'acf/field_group/render_options',
            //         // 'action' : 'advanced-product/field_group/render_options',
            //         'post_id' : acf.post_id,
            //         'field_key' : select.attr('name'),
            //         'field_type' : val,
            //         'nonce' : acf.nonce
            //     };
            //
            //     $.ajax({
            //         url: ajaxurl,
            //         data: ajax_data,
            //         type: 'post',
            //         dataType: 'html',
            //         success: function(html){
            //
            //             if( ! html )
            //             {
            //                 tr.remove();
            //                 return;
            //             }
            //
            //             tr.replaceWith(html);
            //
            //         }
            //     });
            // }

        });

        $(document).on('change', '#ap_meta_box_field_type tr.conditional-logic input[type="radio"]', function( e ){

            e.preventDefault();

            ap_custom_field_change_toggle( $(this) );

        });


        $(document).on('click', '#ap_meta_box_field_type tr.conditional-logic .acf-button-add', function( e ){

            e.preventDefault();

            ap_custom_field_add( $(this).closest('tr') );

        });

        $(document).on('click', '#ap_meta_box_field_type tr.conditional-logic .acf-button-remove', function( e ){

            e.preventDefault();

            ap_custom_field_remove( $(this).closest('tr') );

        });

        $(document).on('change', '#ap_meta_box_field_type select.conditional-logic-field', function( e ){

            e.preventDefault();

            ap_custom_field_change_trigger( $(this) );

        });

        var ap_custom_field_change_toggle = function( $input ){

            // vars
            var val = $input.val(),
                $tr = $input.closest('tr.conditional-logic');


            if( val == "1" )
            {
                $tr.find('.contional-logic-rules-wrapper').show();

                $tr.find("select.conditional-logic-field").trigger("change");
            }
            else
            {
                $tr.find('.contional-logic-rules-wrapper').hide();
            }

        };

        var ap_custom_field_remove = function( $tr ){

            var $table = $tr.closest('table');

            // validate
            if( $table.hasClass('remove-disabled') )
            {
                return false;
            }


            // remove tr
            $tr.remove();


            // add clas to table
            if( $table.find('tr').length <= 1 )
            {
                $table.addClass('remove-disabled');
            }

        };

        var ap_custom_field_add = function( $old_tr ){

            // vars
            var $new_tr = $old_tr.clone(),
                old_i = parseFloat( $old_tr.attr('data-i') ),
                new_i = acf.helpers.uniqid();


            // console.log($new_tr.find('[name]'));
            // update names
            $new_tr.find('[name]').each(function(){

                // flexible content uses [0], [1] as the layout index. To avoid conflict, make sure we search for the entire conditional logic string in the name and id
                var find = '[conditional_logic][rules][' + old_i + ']',
                    replace = '[conditional_logic][rules][' + new_i + ']';

                $(this).attr('name', $(this).attr('name').replace(find, replace) );
                $(this).attr('id', $(this).attr('id').replace(find, replace) );

            });


            // update data-i
            $new_tr.attr('data-i', new_i);


            // add tr
            $old_tr.after( $new_tr );


            // remove disabled
            $old_tr.closest('table').removeClass('remove-disabled');

        };

        var ap_custom_field_change_trigger = function( $select ){

            // vars
            var val			= $select.val(),
                $trigger	= $('.field_key-' + val),
                type		= $trigger.attr('data-type'),
                $value		= $select.closest('tr').find('.conditional-logic-value'),
                choices		= [];

            // populate choices
            if( type == "true_false" )
            {
                choices = [
                    { value : 1, label : acf.l10n.checked }
                ];

            }
            else if( type == "select" || type == "checkbox" || type == "radio" )
            {
                var field_choices = $trigger.find('.field_option-choices').val().split("\n");

                if( field_choices )
                {
                    for( var i = 0; i < field_choices.length; i++ )
                    {
                        var choice = field_choices[i].split(':');

                        var label = choice[0];
                        if( choice[1] )
                        {
                            label = choice[1];
                        }

                        choices.push({
                            'value' : $.trim( choice[0] ),
                            'label' : $.trim( label )
                        });

                    }
                }

            }else if($select.hasClass("select")){
                choices = false;

                var __tr    = $select.closest("tr");

                $value.prop("disabled", true).hide();
                __tr.find(".conditional-logic-value-" + $select.val()).prop("disabled", false).show();
            }

            if(choices !== false) {
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

    });

    // $(document).ready(function(){
    //     console.log(acf);
    //     console.log($('tr.conditional-logic input[type="radio"]'));
    //     acf.conditional_logic.init();
    // });
})(jQuery);