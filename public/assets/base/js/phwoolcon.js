/*! phwoolcon v1.0-dev | Apache-2.0 */
!function (w, $) {
    var options = $.extend({
        cookies: {
            domain: null,
            path: "/"
        }
    }, w.phwoolconOptions);
    w.$p = {
        options: options,
        cookie: function (name, value, options) {
            return $.cookie(name, value, $.extend({}, w.$p.options.cookies, options));
        }
    };
}(window, jQuery);
