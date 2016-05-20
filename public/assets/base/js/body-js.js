!function (w, d, $) {
    $('#locale-selector').on('change', function (e) {
        $p.cookie('locale', this.value);
        w.location.reload();
    });
    var nav = $('header nav');
    nav.on('click', function (e) {
        nav.addClass('active');
        e.stopPropagation();
    });
    $(d).on('click', function (e) {
        nav.removeClass('active');
    });
}(window, document, jQuery);
