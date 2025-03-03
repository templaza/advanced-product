(function($){
    "use strict";

    var advanced_product    = window.advanced_product|| {};

    var l10n    = advanced_product.l10n||{
        "compare": "In compare list"
    };

    advanced_product.__setCookie  = function(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    };

    advanced_product.__getCookie  = function(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    };

    advanced_product.eraseCookie = function (name) {
        advanced_product.__setCookie(name, "", -1);
    };

    advanced_product.arrayAsString   = function(array) {
        var ret = "<#&type=ArrayVals>"; //escapes, tells that string is array
        for (var i = 0; i < array.length; i++) {
            ret = ret + "," + advanced_product.clean(array[i]);
        }
        return ret;
    };

    advanced_product.replaceAll   = function(str, find, replace) {
        return str.replace(new RegExp(find, 'g'), replace);
    };

    advanced_product.dirty   = function(ret) {
        ret = advanced_product.replaceAll(ret, '&#44', ',');
        ret = advanced_product.replaceAll(ret, '&#59', ';');
        return ret;
    };

    advanced_product.clean   = function(ret) {
        ret = advanced_product.replaceAll(ret.toString(), ',', '&#44');
        ret = advanced_product.replaceAll(ret.toString(), ';', '&#59');
        return ret;
    };

    advanced_product.getMyCookie = function(){
        var cstring = advanced_product.__getCookie("advanced-product");


        if (cstring.indexOf('<#&type=ArrayVals>') != -1) {

            var carray = cstring.split(',');

            for (var i = 0; i < carray.length; i++) {
                carray[i] = advanced_product.dirty(carray[i]);
            }

            if (carray[0] == '<#&type=ArrayVals>') {
                carray.splice(0, 1);
            }

            return carray;

        } else {
            var mycookie    = advanced_product.dirty(cstring);
            mycookie    = mycookie.length?JSON.parse(mycookie):{};

            return mycookie;
        }
    };
    advanced_product.setCookie  = function(cname, cvalue, exdays) {
        var _cookie = advanced_product.getMyCookie();

        _cookie[cname]  = cvalue;

        advanced_product.__setCookie("advanced-product", JSON.stringify(_cookie), exdays);
    };
    advanced_product.getCookie  = function(cname) {

        var _cookie = advanced_product.getMyCookie();

        return _cookie[cname];
    };

    advanced_product.addCookieValue  = function(cname, cvalue) {
        var _cookie = advanced_product.getCookie(cname);

        if($.inArray(cvalue, _cookie) === -1){
            _cookie.push(cvalue);
        }
        advanced_product.setCookie(cname, _cookie);
    };

    advanced_product.eraseCookieValue  = function(cname, cvalue) {
        var _cookie = advanced_product.__getCookie(cname).split("|");

        if(!cname || !cvalue){
            return false;
        }

        if(_cookie.indexOf(cvalue.toString()) !== -1){
            var _index    = _cookie.indexOf(cvalue.toString());
            _cookie.splice(_index, 1);
        }
        if(_cookie.length) {
            advanced_product.__setCookie(cname, _cookie.join("|"));
        }else{
            advanced_product.eraseCookie("advanced-product__compare-list");
        }
    };

    advanced_product.list_compare   = function(){
        if($("#tmpl-ap-templates-modal__compare-list").length) {

            var __preloader = $("#tmpl-ap-templates__compare-preloader").length?wp.template("ap-templates__compare-preloader"):false;
            var __preloader_html    = __preloader?$(__preloader({})):"";

            if(__preloader_html) {
                if (!$("body").hasClass("uk-position-relative")) {
                    $("body").addClass("uk-position-relative");
                }
                $("body").prepend(__preloader_html);
            }

            $.ajax({
                url: advanced_product.ajaxurl,
                method: 'POST',
                data: {
                    // post_type: "templaza_library",
                    // title: name,
                    action: "advanced-product/shortcode/advanced-product/compare-list",
                    pid: advanced_product.__getCookie("advanced-product__compare-list").split("|")
                    // section: JSON.stringify(__sec_settings),
                }
            }).done(function(response){
                var __html  = response.data||"";

                if(__preloader_html) {
                    $("body").find(__preloader_html).remove();
                }

                if(!__html.length){
                    UIkit.notification("No matching results", {"status":"danger", "pos": "top-right"});
                    return;
                }

                var $compare_modal = wp.template("ap-templates-modal__compare-list");

                var $compare_modal_html = $($compare_modal({"content": __html}));
                if($("#wpadminbar").length){
                    $compare_modal_html.addClass("uk-margin-top");
                }

                if($("#ap-product-modal").length) {
                    $("#ap-product-modal").remove();
                }
                UIkit.modal($compare_modal_html).show();
            });
        }
    };

    advanced_product.prepare_attribute = function(data_attribute){
        var _attributes = {};
        if(data_attribute !== undefined){
            if(typeof data_attribute === "string"){
                data_attribute  = data_attribute.split(";");
                if(data_attribute.length) {
                    $.each(data_attribute, function (optkey, optval) {
                        var _optsplit   = optval.split(":");

                        if(_optsplit.length) {
                            if(_optsplit.length > 1) {
                                _attributes[_optsplit[0].trim().toLowerCase()]  = _optsplit[1].trim();
                            }else{
                                _attributes["id"]  = _optsplit[0];
                            }
                        }
                    });
                }
            }else{
                _attributes["id"]  = data_attribute;
            }
        }
        return _attributes;
    };

    /* Compare button add */
    $(document).on("click", "[data-ap-compare-button]", function(e){
        if($(this).hasClass("ap-in-compare-list")){
            advanced_product.list_compare();
            return;
        }

        // Get compare button data options
        var _c_options    = $(this).data("ap-compare-button");
        var _compare_options = advanced_product.prepare_attribute(_c_options);

        var _pid    = _compare_options["id"];
        // var _pid    = $(this).data("ap-compare-button");

        if(_pid === undefined || !_pid){
            return false;
        }

        var _pids   = advanced_product.__getCookie("advanced-product__compare-list");
        var pids    = _pids.length?_pids.split("|"):[];

        if(pids.indexOf(_pid.toString()) === -1){
            pids.push(_pid);
        }

        advanced_product.__setCookie("advanced-product__compare-list", pids.join("|"));

        if(UIkit !== undefined) {
            if(_compare_options["active_icon"] !== undefined && _compare_options["active_icon"].length) {
                $(this).find(".js-ap-icon").attr("class", _compare_options["active_icon"] + " js-ap-icon");
            }

            if($(this).attr("uk-tooltip") !== undefined || $(this).attr("data-uk-tooltip") !== undefined) {
                var _tooltip_key    = $(this).attr("uk-tooltip") !== undefined?"uk-tooltip":"data-uk-tooltip";
                $(this).attr(_tooltip_key, _compare_options["active_text"]?_compare_options["active_text"]:l10n.compare.active_text);

            }
            if($(this).find(".js-ap-text").length) {
                $(this).find(".js-ap-text").text(l10n.compare.active_text);
            }
            $(this).addClass("ap-in-compare-list");

            if($("[data-ap-compare-list-button]").length){
                $("[data-ap-compare-count]").text(pids.length);
                $("[data-ap-compare-list-button]").removeClass("uk-hidden").addClass("ap-compare-has-product");
            }

            UIkit.notification(l10n.compare.add_product_successfully, {"status":"success", "pos": "top-right","timeout": "2000"});
        }
    });

    /* Compare list button */
    $(document).on("click", "[data-ap-compare-list-button]", function(){
        if(!$(this).hasClass("ap-compare-has-product")){
            return false;
        }
        advanced_product.list_compare();
    });

    /* Compare remove product button */
    $(document).on("click", "[data-ap-compare-delete-button]", function(e){
        e.preventDefault();

        var __btn  = $(this);

        UIkit.modal.confirm(l10n.compare.delete_question,{
            "stack": true
        }).then(function() {
            var _c_options    = __btn.data("ap-compare-delete-button");
            var _compare_options = advanced_product.prepare_attribute(_c_options);

            var _pid    = _compare_options["id"];

            if(_pid === undefined || !_pid){
                return false;
            }

            advanced_product.eraseCookieValue("advanced-product__compare-list", _pid);
            var _sc_item    = $("[data-ap-compare-button*=\"id: "+_pid+"\"]"),
                _sc_option  = advanced_product.prepare_attribute(_sc_item.data("ap-compare-button"));

            if(_sc_item.length){
                _sc_item.removeClass("ap-in-compare-list");
                _sc_item.find(".js-ap-icon").attr("class", _sc_option["icon"] + " js-ap-icon");
                if(_sc_item.find(".js-ap-text").length) {
                    _sc_item.find(".js-ap-text").text(l10n.compare.text);
                }
                if(_sc_item.attr("data-uk-tooltip").length){
                    _sc_item.attr("data-uk-tooltip", l10n.compare.text);
                }
            }

            __btn.closest(".ap-product-compare-item").remove();

            if($("[data-ap-compare-list-button]").length){
                var _pids   = advanced_product.__getCookie("advanced-product__compare-list");
                var pids    = _pids.length?_pids.split("|"):[];
                $("[data-ap-compare-count]").text(pids.length);
                $("[data-ap-compare-list-button]").removeClass("ap-compare-has-product").addClass("uk-hidden");
            }
        },function(){});

    });

    /* Compare close button */
    $(document).on("click", ".ap-compare-close", function(){
        $(this).parent().toggleClass('active');
    });

    // Quick view button
    $(document).on("click", "[data-ap-quickview-button]", function(event){
        var _compare_options = advanced_product.prepare_attribute($(this).data('ap-quickview-button'));

        var _pid    = _compare_options["id"];

        if(_pid === undefined || !_pid){
            return false;
        }


        var __preloader = $("#tmpl-ap-templates__compare-preloader").length?wp.template("ap-templates__compare-preloader"):false;
        var __preloader_html    = __preloader?$(__preloader({})):"";

        if(__preloader_html) {
            if (!$("body").hasClass("uk-position-relative")) {
                $("body").addClass("uk-position-relative");
            }
            $("body").prepend(__preloader_html);
        }

        $.ajax({
            url: advanced_product.ajaxurl,
            method: 'POST',
            data: {
                action: "advanced-product/shortcode/advanced-product/quick-view",
                pid: _pid
            }
        }).done(function(response){

            if($("#tmpl-ap-templates-modal__quickview").length){

                var __html  = response.data||"";

                if(__preloader_html) {
                    $("body").find(__preloader_html).remove();
                }

                if(!__html.length){
                    UIkit.notification("No matching results", {"status":"danger", "pos": "top-right"});
                    return;
                }

                var $compare_modal = wp.template("ap-templates-modal__quickview");

                var $compare_modal_html = $($compare_modal({"content": __html}));
                if($("#wpadminbar").length){
                    $compare_modal_html.addClass("uk-margin-top");
                }

                if($("#ap-product-modal__quickview").length) {
                    $("#ap-product-modal__quickview").remove();
                }
                UIkit.modal($compare_modal_html).show();
            }
        });
    });

    // Search form change
    $(document).on("change", "form.advanced-product-search-form", function(event) {
        var __form  = $(this),
            __form_setting = __form.attr("data-ap-settings");
        __form_setting  = typeof __form_setting == "string"?JSON.parse(__form_setting):__form_setting;
        var __is_ajax   = false;

        if(__form_setting !== undefined && __form_setting && __form_setting['enable_ajax'] !== undefined
            && __form_setting['enable_ajax'] && __form_setting['instant'] !== undefined && __form_setting['instant']) {
            __is_ajax   = true;
        }

        if(__is_ajax) {
            $('.templaza-ap-archive').addClass('tz-loading').append('<div class="templaza-posts__loading show"><span class="templaza-loading"></span> </div>');

            var __form_data = __form.serializeArray();

            var __data          = [];
            var __ajax_options  = "";

            // Get archive view
            if ($("[data-ap-archive-view]").data("ap-archive-view") !== undefined) {
                __data.push("archive_view=" + $("[data-ap-archive-view]").data("ap-archive-view"));
            }

            // Get sort order
            if ($(".templaza-ap-archive-sort select").length) {
                __data.push("sort_order=" + $(".templaza-ap-archive-sort select").val());
            }

            // Preprocess form data
            if (__form_data.length) {
                var __url_data  = new URLSearchParams(window.location.search);
                var __el    = $(event.target);

                $.each(__form_data, function (index, item) {
                    if(__form.attr('data-filter')== item.name){
                        if (item.value.length && (__form.attr('data-filter')== item.name || __url_data.has(item.name))) {
                            __data.push(item.name + "=" + item.value);
                        }
                    }else{
                        if (item.value.length && (__el.attr("name") == item.name || __url_data.has(item.name))) {
                            __data.push(item.name + "=" + item.value);
                        }
                    }
                });
            }

            if (__data.length) {
                __ajax_options = __data.join("&");
            }

            $.get(__form.attr("action"), __ajax_options, function (data) {
                // Replace html filtered
                $(".templaza-ap-archive").replaceWith($(data).find(".templaza-ap-archive"));
                $('.templaza-ap-archive').find('.ap-item').each(function (index, product) {
                    $(product).css("animation-delay", index * 100 + "ms");
                });

                // Replace pagination
                if ($(data).find(".templaza-blog-pagenavi").length) {
                    $(".templaza-blog-pagenavi").show().html($(data).find(".templaza-blog-pagenavi").html());
                } else {
                    $(".templaza-blog-pagenavi").hide();
                }

                // Replace product number
                if($(".ap-number-product").length){
                    if($(data).find(".ap-number-product").length){
                        $(".ap-number-product").html($(data).find(".ap-number-product").html());
                    }else{
                        $(".ap-number-product").html("");
                    }
                }

                if($("[data-ap-archive-view]").data("ap-archive-view") !== undefined) {
                    $("[data-ap-archive-view]").data("ap-archive-view-loaded", true);
                    $("[data-ap-archive-view]").trigger("ap-archive-view-loaded");
                }

                // Replace current url without redirect
                if (__form_setting["update_url"]) {
                    window.history.pushState({urlPath: location.href}, "", this.url);
                }
            });
        }
    });

    $(document).on("ap-archive-view-loaded", "[data-ap-archive-view]", function(){
        var __view = $(this).data("ap-archive-view"),
            __childs = $(this).find("[data-ap-archive-view-item]"),
            __cindex = __childs.index($(this).find("[data-ap-archive-view-item="+ __view +"]"));
        UIkit.switcher(this).show(__cindex);
    });

    // Search form submit
    $(document).on("submit", "form.advanced-product-search-form", function(event){
        var __form  = $(this),
            __form_data  = $(this).serializeArray();

        // Preprocess form data
        if(__form_data.length){
            $.each(__form_data, function(index, item){
                if(!item.value.length){
                    var __name  = "[name=" + item.name.replace(/(\[|\])/gi, "\\$1") + "]";
                    __form.find(__name).prop("disabled", true);
                }
            });
        }

        return true;
    });

    // Search submit button click
    $(document).on("click", "form.advanced-product-search-form .car-search-submit",function(event){
        event.preventDefault();

        var __form  = $(this).closest("form.advanced-product-search-form"),
            __form_setting = __form.attr("data-ap-settings");
        __form_setting  = typeof __form_setting == "string"?JSON.parse(__form_setting):__form_setting;
        var __is_ajax   = false;

        if(__form_setting !== undefined && __form_setting && __form_setting['enable_ajax'] !== undefined
            && __form_setting['enable_ajax'] && __form_setting['instant'] !== undefined && !__form_setting['instant']) {
            __is_ajax   = true;
        }

        if(!__is_ajax) {
            __form.submit();
            return;
        }

        var __form_data  = __form.serializeArray();

        var __data = [];
        var __ajax_options = '';

        // Get archive view
        if($("[data-ap-archive-view]").data("ap-archive-view") !== undefined){
            __data.push("archive_view=" + $("[data-ap-archive-view]").data("ap-archive-view"));
        }

        // Get sort order
        if($(".templaza-ap-archive-sort select").length){
            __data.push("sort_order=" + $(".templaza-ap-archive-sort select").val());
        }

        // Preprocess form data
        if(__form_data.length){
            $.each(__form_data, function(index, item){
                if(item.value.length){
                    __data.push(item.name + "=" + item.value);
                }
            });
        }

        if(__data.length){
            __ajax_options  = __data.join("&");
        }

        $.get(__form.attr("action"), __ajax_options, function (data) {

            // Replace html filtered
            $(".templaza-ap-archive").replaceWith($(data).find(".templaza-ap-archive"));

            // Replace pagination
            if ($(data).find(".templaza-blog-pagenavi").length) {
                $(".templaza-blog-pagenavi").show().html($(data).find(".templaza-blog-pagenavi").html());
            } else {
                $(".templaza-blog-pagenavi").hide();
            }

            // Replace product number
            if($(".ap-number-product").length){
                if($(data).find(".ap-number-product").length){
                    $(".ap-number-product").html($(data).find(".ap-number-product").html());
                }else{
                    $(".ap-number-product").html("");
                }
            }

            // Replace current url without redirect
            if (__form_setting['update_url']) {
                window.history.pushState({urlPath: location.href}, "", this.url);
            }
        });
    });

    advanced_product.__archive_ajax_html = function(ajax_options, ajax_url, options){
        $(".templaza-ap-archive").addClass("tz-loading")
            .append('<div class="templaza-posts__loading show"><span class="templaza-loading"></span></div>');

        options     = options !== undefined?options:{};

        var __url   = new URL(location.href);

        ajax_url    = ajax_url !== undefined?ajax_url:__url.origin + __url.pathname;

        var __data = [],
            __ajax_options = '';

        // Get archive view
        if($("[data-ap-archive-view]").data("ap-archive-view") !== undefined){
            __data.push("archive_view=" + $("[data-ap-archive-view]").data("ap-archive-view"));
        }

        // Get sort order
        if($(".templaza-ap-archive-sort select").length){
            __data.push("sort_order=" + $(".templaza-ap-archive-sort select").val());
        }

        __data  = (ajax_options !== undefined && Array.isArray(ajax_options))?[...__data, ...ajax_options]:__data;

        if(__data.length){
            __ajax_options  = __data.join("&");
        }

        $.get(ajax_url, __ajax_options, function (data) {
            // Replace html filtered
            $(".templaza-ap-archive").replaceWith($(data).find(".templaza-ap-archive"));
            $('.templaza-ap-archive').find('.ap-item').each(function (index, product) {
                $(product).css('animation-delay', index * 100 + 'ms');
            });

            // Replace pagination
            if ($(data).find(".templaza-blog-pagenavi").length) {
                $(".templaza-blog-pagenavi").show().html($(data).find(".templaza-blog-pagenavi").html());
            } else {
                $(".templaza-blog-pagenavi").hide();
            }

            if($("[data-ap-archive-view]").data("ap-archive-view") !== undefined) {
                $("[data-ap-archive-view]").data("ap-archive-view-loaded", true);
                $("[data-ap-archive-view]").trigger("ap-archive-view-loaded");
            }

            // Replace current url without redirect
            if (options["update_url"] !== undefined && options["update_url"]) {
                var __urlPath   = options["urlPath"] !== undefined?options["urlPath"]:false;
                var __urlOption = {};

                if(__urlPath){
                    __urlOption["urlPath"] = __urlPath;
                }

                window.history.pushState(__urlOption, "", this.url);
            }
        });
    };

    // Grid view
    $(document).on("click", "[data-ap-archive-view] [data-ap-archive-view-item]", function(event){

        var __el = $(this),
            __parent = __el.closest("[data-ap-archive-view]"),
            __loaded = __parent.data("ap-archive-view-loaded") !== undefined?__parent.data("ap-archive-view-loaded"):true,
            __grid_view = __parent.data("ap-archive-view");

        if(__grid_view === __el.attr("data-ap-archive-view-item") || !__loaded){
            UIkit.switcher(__parent[0]).show(__parent.find("[data-ap-archive-view-item]").index(
                __parent.find("[data-ap-archive-view-item="+__grid_view+"]")));
            return false;
        }

        if(__el.attr("data-ap-archive-view-item") !== undefined){
            __parent.data("ap-archive-view", __el.attr("data-ap-archive-view-item"));
        }

        if(__grid_view !== __parent.data("ap-archive-view")) {
            __parent.data("ap-archive-view-loaded", false);
            if($("form.advanced-product-search-form").length) {
                $("form.advanced-product-search-form").trigger("change");
            }else if($(".templaza-ap-archive").length){
                advanced_product.__archive_ajax_html(undefined, undefined, {"update_url": true});
            }
        }
    });
    if($("form.advanced-product-search-form .field-keyword").length) {
        $("#search-keyword").autocomplete({
            minLength: 1,
            source: function (request, response) {
                // well use opts.ajax_url which we enqueued with WP
                $.get(advanced_product.ajaxurl, {
                        action: 'advanced_autocomplete_search',  // our action is called search
                        title: request.term
                    }, function (data) {
                        // when we get data from ajax, we pass it onto jq-ui autocomplete
                        response(data);
                    }, 'json'
                );
            },
            appendTo: "#advanced-search-results",
            focus: function (event, ui) {
                $("#search-keyword").val(ui.item.label);
                return false;
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                .append("<a href=" + item.link + ">" + item.label + "<span>" + item.id + "</span></a>")
                .appendTo(ul);
        };
    }

    // Sort order
    $(document).on("change",".templaza-ap-archive-sort select", function(event){
        if($("form.advanced-product-search-form").length) {
            $("form.advanced-product-search-form").trigger("change");
        }else if($(".templaza-ap-archive").length){
            advanced_product.__archive_ajax_html(undefined, undefined, {"update_url": true});
        }
    });

    // Filter form ajax
    $(document).ready(function(){
        if($('.ap-search-max-height').length){
            $('body').addClass('body-ap-search-max-height');
            $('.ap-search-expand').on('click',function(){
                $(this).removeClass('active');
                $(this).parent().find('.ap-search-shrink').addClass('active');
                var ap_filter_height = $(this).parent().find('.advanced-product-search-form').outerHeight();
                $(this).parents('.ap-search-max-height').css('height',ap_filter_height+'px');
            });
            $('.ap-search-shrink').on('click',function(){
                $(this).removeClass('active');
                $(this).parent().find('.ap-search-expand').addClass('active');
                $(this).parents('.ap-search-max-height').removeAttr('style');
            });
            $('.ap-search-mini').on('click',function(){
                $(this).removeClass('active');
                $(this).parent().find('.ap-search-full').addClass('active');
                $(this).parents('.ap-search-max-height').addClass('closed');
                $(this).parents('.uk-sticky').addClass('closed');
            });
            $('.ap-search-full').on('click',function(){
                $(this).removeClass('active');
                $(this).parent().find('.ap-search-mini').addClass('active');
                $(this).parents('.ap-search-max-height').removeClass('closed');
                $(this).parents('.uk-sticky').removeClass('closed');
            });
        }
        if($('.ap-compare-btn-wrap').length){
            $('.ap-compare-close').on('click',function(){
                $(this).parent().toggleClass('closed');
            });
        }
        if($('.ap_product_quote').length){
            $('.ap_product_quote span.quote_close').on('click',function(){
                $(this).parents('.ap_product_quote').toggleClass('closed');
            })
            $('.ap_product_quote span.quote_open').on('click',function(){
                $(this).parent().toggleClass('closed');
            });
        }
        if($('.templaza-ap-archive').length){
            if($(window).width()<1200){
                $('.templaza-framework-gutenberg-adv-product-filters').parents('.templaza-column').addClass('hideonlg');
                $('.templaza-ap-product-filter, .templaza-filter-closed ').removeClass('uk-hidden@m').addClass('uk-hidden@l');
                $(document.body).on('click', '.ap-filter-btn', function (e) {
                    $('.templaza-framework-gutenberg-adv-product-filters').parents('.templaza-column').toggleClass('sidebar-fixed');
                    $('.templaza-framework-gutenberg-adv-product-filters').parents('section').addClass('fix-index');
                });
                $(document.body).on('click', '.templaza-filter-closed', function (e) {
                    $('.templaza-framework-gutenberg-adv-product-filters').parents('.templaza-column').removeClass('sidebar-fixed');
                    $('.templaza-framework-gutenberg-adv-product-filters').parents('section').removeClass('fix-index');
                });
            }
        }

        /* Associate for form */
        var __adv_custom_field_associate    = function($mainObj){
            if($mainObj.find("[data-field_type=taxonomy]").length) {
                /* Enable or disable taxonomy */
                var _ap_enable_disable_options = function($f_name, $f_value){
                    if(!$f_value || !$f_value.length){
                        $mainObj.find("[data-field_type=taxonomy] [data-associate-from=" + $f_name+"]").prop("disabled", false);
                        return;
                    }

                    var __ap_set_enable_disable_option = function($_f_value, $_disabled = true){

                        var __f_option = $mainObj.find("[data-field_type=taxonomy] [data-associate-from=" + $f_name+"][data-associate~=" + $_f_value+"]");

                        var __main  = $mainObj.find("[data-field_type=taxonomy] [data-associate-from=" + $f_name+"]").closest(".field_type-taxonomy");

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

                    var __main  = $mainObj.find("[data-field_type=taxonomy] [data-associate-from="
                        + $f_name+"]").closest(".acf-taxonomy-field");
                    __main.find("[data-associate-from="+ $f_name+"]").prop("disabled", true);
                    if(typeof $f_value === "object"){

                        $.each($f_value, function(index, f_val){
                            __ap_set_enable_disable_option(f_val, false);
                        });

                    }else{
                        __ap_set_enable_disable_option($f_value);
                    }
                };
                $mainObj.find("[data-field_type=taxonomy]").each(function () {

                    var __f = $(this),
                        __f_name = $(this).attr("data-field_name"),
                        __f_key = $(this).attr("data-field_key"),
                        // __f_control = $(this).find("#acf-field-"+__f_name);
                        __f_control = $(this);

                    if(!__f_control.length) {
                        __f_control = $(this).find("[name^=field\\\[" + __f_name + "\\\]]");
                    }

                    if(__f_control.length) {
                        $.each(__f_control, function () {
                            var __f_value   = typeof $(this).val() !== "undefined"?$(this).val():"";
                            if (typeof $(this).prop("checked") !== "undefined" && !$(this).prop("checked")) {
                                __f_value   = "";
                            }

                            _ap_enable_disable_options(__f_name, __f_value);
                        });
                    }

                    __f_control.off("change").on("change", function() {
                        var __f_input = $(this).find("[name^=field\\\[" + __f_name + "\\\]]");
                        var __f_value = __f_input.advSerializeObject();

                        if ((typeof __f_value === "object" && !Object.keys(__f_value).length) || (typeof __f_value !== "object" && !__f_value.length)) {
                            $.each(__f_control, function () {
                                _ap_enable_disable_options(__f_name, "");
                            });
                            return;
                        }

                        __f_value   = __f_value["field"][__f_name];

                        _ap_enable_disable_options(__f_name, __f_value);
                    });

                });
            }
        };

        __adv_custom_field_associate($("form.advanced-product-search-form"));
    });

})(jQuery);