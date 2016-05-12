(function (w, d, $) {
    $('#locale-selector').on('change', function (e) {
        $p.cookie('locale', this.value);
        w.location.reload();
    });
})(window, document, jQuery);
