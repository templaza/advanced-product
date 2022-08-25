(function($){
    "use strict";

    // $.fn.advanced_product   = function(){
    //
    // };
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
        // advanced_product.__setCookie("advanced-product", "", -1);
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

        // var _cookie = advanced_product.__getCookie("advanced-product");
        // console.log("getMyCookie");
        // console.log(_cookie);
        // return false;
        //
        // return _cookie.length?JSON.parse(_cookie):{};
    };
    advanced_product.setCookie  = function(cname, cvalue, exdays) {
        var _cookie = advanced_product.getMyCookie();
        // var _cookie = advanced_product.__getCookie("advanced-product");

        // _cookie = _cookie.length?JSON.parse(_cookie):{};

        _cookie[cname]  = cvalue;

        // advanced_product.__setCookie("advanced-product", advanced_product.arrayAsString(_cookie), exdays);
        advanced_product.__setCookie("advanced-product", JSON.stringify(_cookie), exdays);
    };
    advanced_product.getCookie  = function(cname) {

        var _cookie = advanced_product.getMyCookie();

        // var _cookie = advanced_product.__getCookie("advanced-product");
        //
        // _cookie = _cookie.length?JSON.parse(_cookie):{};

        // _cookie = (_cookie !== undefined && _cookie.length)?JSON.parse(_cookie):{};

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
        var _cookie = advanced_product.__getCookie(cname).split("|").map(function (number) {
            return parseInt(number, 10);
        });

        if(!cname || !cvalue){
            return false;
        }

        if(_cookie.indexOf(cvalue) !== -1){
            var _index    = _cookie.indexOf(cvalue);
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
                    UIkit.notification("No matching results", {"status":"danger", "pos": "bottom-right"});
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

    advanced_product.prepare_data_compare = function(data_compare){
        var _compare_options = {};
        // var data_compare    = $(this).data("ap-compare-button");
        if(data_compare !== undefined){
            if(typeof data_compare === "string"){
                data_compare  = data_compare.split(";");
                if(data_compare.length) {
                    $.each(data_compare, function (optkey, optval) {
                        var _optsplit   = optval.split(":");

                        if(_optsplit.length) {
                            if(_optsplit.length > 1) {
                                _compare_options[_optsplit[0].trim().toLowerCase()]  = _optsplit[1].trim();
                            }else{
                                _compare_options["id"]  = _optsplit[0];
                            }
                        }
                    });
                }
            }else{
                _compare_options["id"]  = data_compare;
            }
        }
        return _compare_options;
    };

    /* Compare button add */
    $(document).on("click", "[data-ap-compare-button]", function(e){
        if($(this).hasClass("ap-in-compare-list")){
            advanced_product.list_compare();
            return;
        }

        // Get compare button data options
        var _c_options    = $(this).data("ap-compare-button");
        var _compare_options = advanced_product.prepare_data_compare(_c_options);

        var _pid    = _compare_options["id"];
        // var _pid    = $(this).data("ap-compare-button");

        if(_pid === undefined || !_pid){
            return false;
        }

        var _pids   = advanced_product.__getCookie("advanced-product__compare-list");
        var pids    = _pids.length?_pids.split("|"):[];

        // if($.inArray(_pid.toString(), pids) === -1){
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

            UIkit.notification(l10n.compare.add_product_successfully, {"status":"success", "pos": "bottom-right"});
        }
    });

    /* Compare list button */
    $(document).on("click", "[data-ap-compare-list-button]", function(){
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
            var _compare_options = advanced_product.prepare_data_compare(_c_options);

            var _pid    = _compare_options["id"];

            if(_pid === undefined || !_pid){
                return false;
            }

            // advanced_product.eraseCookieValue("advanced-product__compare-list", _pid);
            var _sc_item    = $("[data-ap-compare-button*=\"id: "+_pid+"\"]"),
                _sc_option  = advanced_product.prepare_data_compare(_sc_item.data("ap-compare-button"));

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
        },function(){});

    })
})(jQuery);