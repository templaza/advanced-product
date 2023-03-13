
jQuery( function($) {
    if($("[data-ap-range-slider]").length) {
        $("[data-ap-range-slider]").each(function(){
            var __slider = $(this),
                __range = __slider.find(".ap-slider-range"),
                __settings = __slider.data("ap-range-slider");
            var __defsetings = {
                "min": 0,
                "max": 0,
                "step": 1,
                "symbol": "$",
                "placement": "prepend",
            };

            __settings  = Object.assign(__defsetings,__settings);

            __range.slider({
                range: true,
                step: __settings["step"],
                min: __settings["min"],
                max: __settings["max"],
                values: [__settings["min"], __settings["max"]],
                slide: function (event, ui) {
                    var __symbol = __settings["symbol"],
                        __place = __settings["placement"],
                        __min = ui.values[0],
                        __max = ui.values[1];

                    if (__slider.length) {
                        if(__slider.find("[data-ap-range-min]").length){
                            __slider.find("[data-ap-range-min]").val(__min);
                        }
                        if(__slider.find("[data-ap-range-max]").length){
                            __slider.find("[data-ap-range-max]").val(__max);
                        }
                    }

                    if (__place === "append") {
                        __min += __symbol;
                        __max += __symbol;
                    } else {
                        __min = __symbol + __min;
                        __max = __symbol + __max;
                    }

                    if (__slider.length && __slider.find(".ap-slider-number-label")) {
                        __slider.find(".ap-slider-number-label .from").text(__min);
                        __slider.find(".ap-slider-number-label .to").text(__max);
                    }
                },
                stop: function( event, ui ) {
                    var __form = $(this).closest("form");
                    __form.trigger("change");
                }
            });
        });
    }
} );