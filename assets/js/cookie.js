(function($){
    "use strict";

    // var cookiemonster = new Object();
    var advanced_product    = advanced_product|| {};

    advanced_product.append = function (cookieName, item, expDays) {
        item = cm_clean(item);
        var cookievalue = cookiemonster.get(cookieName);
        if (cookievalue instanceof Array) {
            cookievalue[cookievalue.length] = item;
            cm_createCookie(cookieName, cm_arrayAsString(cookievalue), expDays);
        } else {
            cm_createCookie(cookieName, cookievalue + item, expDays);
        }
    };

    advanced_product.splice = function (cookieName, index, numberToRemove, expDays) {
        var cookievalue = cookiemonster.get(cookieName);
        if (cookievalue instanceof Array) {
            cookievalue.splice(index, numberToRemove);
            cm_createCookie(cookieName, cm_arrayAsString(cookievalue), expDays);
        }
    };

    advanced_product.get = function (cookieName) {
        var cstring = cm_readCookie(cookieName);
        if (cstring.indexOf('<#&type=ArrayVals>') != -1) {

            var carray = cstring.split(',');

            for (var i = 0; i < carray.length; i++) {
                carray[i] = cm_dirty(carray[i]);
            }

            if (carray[0] == '<#&type=ArrayVals>') {
                carray.splice(0, 1);
            }

            return carray;

        } else {

            return cm_dirty(cstring);
        }
    };

    advanced_product.set = function (cookieName, value, expDays) {
        if (value instanceof Array) {
            cm_createCookie(cookieName, cm_arrayAsString(value), expDays);
        }
        else { cm_createCookie(cookieName, cm_clean(value), expDays); }

    };

    advanced_product.eraseCookie = function (name) {
        cm_createCookie(name, "", -1);
    };

    function cm_replaceAll(str, find, replace) {

        return str.replace(new RegExp(find, 'g'), replace);
    }

    function cm_clean(ret) {
        ret = cm_replaceAll(ret.toString(), ',', '&#44');
        ret = cm_replaceAll(ret.toString(), ';', '&#59');
        return ret;
    }
    function cm_dirty(ret) {
        ret = cm_replaceAll(ret, '&#44', ',');
        ret = cm_replaceAll(ret, '&#59', ';');
        return ret;
    }

    function cm_createCookie(name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        } else var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    function cm_readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function cm_arrayAsString(array) {
        var ret = "<#&type=ArrayVals>"; //escapes, tells that string is array
        for (var i = 0; i < array.length; i++) {
            ret = ret + "," + cm_clean(array[i]);
        }
        return ret;
    }
})(jQuery);