(function($){
    "use strict";

    var APIconFieldConfig   = window.APIconFieldConfig||{};

    APIconFieldConfig.__generate_icon_html  = function($current_tab){
        var __icon_list = $("#tmpl-ap-template-field__ap-icon").length?wp.template("ap-template-field__ap-icon"):false;
        var __html  = "Not found icons";

        $("#ap-field-icon-content").html("<div data-uk-spinner></div>");

        if(APIconFieldConfig === undefined || !__icon_list.length){
            $("#ap-field-icon-content").html(__html);
            UIkit.switcher("#ap-field-icon-content").show(0);

            return false;
        }

        var __current_type  = $current_tab.attr("data-ap-tab-item");
        var __icons_source  = APIconFieldConfig.icons;
        var __icons         = {};
        if(__current_type === "ap_all"){
            $.each(__icons_source, function ($key, $icon) {
                if($key === __current_type){
                    return true;
                }

                __icons[$key] = $icon;
            });
        }else{
            __icons[__current_type] = __icons_source[__current_type];
        }

        __html  = $(__icon_list(__icons));

        APIconFieldConfig.__field_queue = __html.clone(true);
        $("#ap-field-icon-content").html(__html);
        UIkit.switcher("#ap-field-icon-content").show(0);

        if($("#ap-fields__icon-library [data-ap-search]").val().length){
            $("#ap-fields__icon-library [data-ap-search]").trigger("keyup");
        }
    };

    // Search
    $(document).on("keyup", "#ap-fields__icon-library [data-ap-search]", function(event){
        event.preventDefault();
        var __fields_list   = APIconFieldConfig.__field_queue.clone(true);
        if($(this).val().length) {
            $("#ap-field-icon-content").children().html(__fields_list.find("[data-ap-filter*=" + $(this).val() + "]"));
        }else{
            $("#ap-field-icon-content").children().html(APIconFieldConfig.__field_queue.clone(true).children());
        }
    });

    // Load icons when modal show
    $(document).ready(function(){
        UIkit.util.on("#ap-fields__icon-library", "shown", function(event){
                APIconFieldConfig.__generate_icon_html($("#ap-field-icon-nav > .uk-active > [data-ap-tab-item]"));
            var __control_clicked   = $(this).closest("#ap-fields__icon-library").data("ap-field-icon__btn-clicked");

            if(__control_clicked !== undefined && __control_clicked.length){
                var __icons_source  = APIconFieldConfig.icons,
                    __parent_control = __control_clicked.closest("[data-field_type=ap_icon]"),
                    __icon_filter = __parent_control.find("[data-ap-field-icon__icon]").val(),
                    __icon_type = __parent_control.find("[data-ap-field-icon__type]").val();

                if(!__icon_filter.length || !__icon_type.length){
                    return false;
                }

                var __icon_source = __icons_source[__icon_type];

                if(__icon_type !== "uikit-icon"){
                    var __regex = new RegExp("^" + __icon_source["displayPrefix"] + " " +__icon_source["prefix"], "i");
                    __icon_filter   = __icon_filter.replace(__regex, "");
                }

                $("#ap-field-icon-content [data-ap-filter="+ __icon_filter+"]").addClass("ap-field-icon-selected")
                    .children(".uk-card").addClass("uk-card-primary");
            }
        });
        UIkit.util.on("#ap-fields__icon-library", "beforehide", function(event){
            $("#ap-fields__icon-library [data-ap-search]").val("");
            $("#ap-field-icon-content").html("");
        });
        UIkit.util.on("#ap-fields__icon-library", "hidden", function(event){
            $("#ap-fields__icon-library").data("ap-field-icon__btn-clicked", "");
        });
    });

    $(document).on("click", "[data-field_type=ap_icon] [data-ap-field-icon-modal]", function(event){
        $("#ap-fields__icon-library").data("ap-field-icon__btn-clicked", $(this));
        UIkit.modal("#ap-fields__icon-library").show();
    });

    $(document).on("click", "#ap-fields__icon-library [data-ap-filter]", function(event){
        $(this).addClass("ap-field-icon-selected")
            .children(".uk-card").addClass("uk-card-primary")
            .end().siblings().removeClass("ap-field-icon-selected")
            .children(".uk-card").removeClass("uk-card-primary");
    });

    // Tab item selected
    $(document).on("click", "#ap-fields__icon-library [data-ap-tab-item]", function(event){
        APIconFieldConfig.__generate_icon_html($(this));
    });

    // Insert icon
    $(document).on("click", "#ap-fields__icon-library [data-ap-field-icon-insert]", function(event){
        var _item_selected  = $("#ap-fields__icon-library #ap-field-icon-content [data-ap-filter].ap-field-icon-selected");
        var __control_clicked   = $(this).closest("#ap-fields__icon-library").data("ap-field-icon__btn-clicked"),
            __parent_control    = __control_clicked.closest("[data-field_type=ap_icon]"),
            __icons_source  = APIconFieldConfig.icons,
            __icon_type = _item_selected.attr("data-ap-icon-type"),
            __icon_value = _item_selected.attr("data-ap-filter"),
            __icon_source   = __icons_source[__icon_type];

        if(__icon_type !== "uikit-icon"){
            __icon_value    = __icon_source.displayPrefix + " "+ __icon_source.prefix + __icon_value;
            __control_clicked.html("<i class=\'"+ __icon_value +" fa-3x\'></i>");
        }else{
            __control_clicked.html("<span data-uk-icon=\'icon: "+ __icon_value +";ratio: 2;\'></span>");
        }

        __parent_control.find("[data-ap-field-icon__type]").val(__icon_type);
        __parent_control.find("[data-ap-field-icon__icon]").val(__icon_value);

        UIkit.modal("#ap-fields__icon-library").hide();
    });

})(jQuery);