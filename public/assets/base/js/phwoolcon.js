/*! phwoolcon v1.0-dev | Apache-2.0 */
(function (w, $) {
    var phwoolconOptions = $.extend({
        cookies: {
            domain: null,
            path: "/"
        }
    }, w.phwoolconOptions);
    w.$p = {
        cookie: function (name, value, options) {
            return $.cookie(name, value, $.extend({}, phwoolconOptions.cookies, options));
        }
    };
})(window, jQuery);
