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
                UIkit.modal($compare_modal_html).show();
            });
        }
    };

    /* Compare button add */
    $(document).on("click", "[data-ap-compare-button]", function(e){
        if($(this).hasClass("active")){
            advanced_product.list_compare();
            return;
        }
        var _pid    = $(this).data("ap-compare-button");

        if(_pid === undefined || !_pid){
            return false;
        }

        var _pids   = advanced_product.__getCookie("advanced-product__compare-list");

        var pids    = _pids.split("|");

        if($.inArray(_pid.toString(), pids) === -1){
            pids.push(_pid);
        }

        advanced_product.__setCookie("advanced-product__compare-list", pids.join("|"));

        if(UIkit !== undefined) {
            $(this).find(".js-ap-icon").attr("class", $(this).data("ap-compare-active-icon"))
                .end().find(".js-ap-text").text(l10n.compare.active_text);
            $(this).addClass("active");
            // $(this).trigger("click");

            UIkit.notification(l10n.compare.add_product_successfully, {"status":"success", "pos": "bottom-right"});
            // UIkit.notification("Add product to compare list successfully!", "success");
        }
    });

    /* Compare list button */
    $(document).on("click", "[data-ap-compare-list-button]", function(){
        advanced_product.list_compare();
    });
})(jQuery);