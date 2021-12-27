jQuery(function($){
    "use strict";

    advanced_product.field_layout = advanced_product.field_layout || {};
    advanced_product.field_layout.select = advanced_product.field_layout.select || {};

    advanced_product.field_layout.select.init   = function () {

        $(document).on("change", ".postbox tr.field-search-option-s_from_to input[type=radio]", function(){
            var __this  = $(this),
                __val   = __this.val(),
                __checked   = __this.is(":checked"),
                __tr    = __this.closest("tr.field-search-option-s_from_to");

            if(__checked){
                if(__val == 1) {
                    __tr.nextAll("tr.field-search-option-s_from_to_0").hide();
                    __tr.nextAll("tr.field-search-option-s_from_to_1").show();
                }else{
                    __tr.nextAll("tr.field-search-option-s_from_to_0").show();
                    __tr.nextAll("tr.field-search-option-s_from_to_1").hide();
                }
            }
        });

        // Recall
        $(document).on("ajaxCompleted changed", ".postbox tr.field_search_type select", function(){
            $(".postbox tr.field-search-option-s_from_to input[type=radio]").trigger("change");
        });

        $(".postbox tr.field-search-option-s_from_to input[type=radio]").trigger("change");
    };
    advanced_product.field_layout.select.init();
});