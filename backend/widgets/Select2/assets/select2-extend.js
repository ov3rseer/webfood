;(function($) {
    $.fn.select2Extend = function(method, options) {
        if (!this.data('select2')) {
            return this;
        }
        return this.each(function() {
            var $select = $(this);
            if (method == 'updateItems') {
                // https://github.com/select2/select2/issues/2830
                var settings = $select.data('select2').options.options;
                $select.html('');
                $.each(options.items, function(i, value) {
                    var $option = $('<option></option>').attr('value', value['id']).text(value['text']);
                    if ($.inArray(value['id'], options.selected)) {
                        $option.attr('selected', 'selected');
                    }
                    $select.append($option);
                });
                settings.items = options.items;
                $select.select2(settings);
            }
        });
    };
}(jQuery));
