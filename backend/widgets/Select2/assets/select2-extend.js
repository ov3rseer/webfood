;(function($) {

    $.fn.select2Extend = function(method, options) {
        if (!this.data('select2')) {
            return this;
        }
        return this.each(function() {
            var $select = $(this);
            if (method === 'updateItems') {
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

    // Сохранение порядка выбранных элементов (в порядке выбора элементов)
    $('select').on('select2:select', function(event) {
        var $select = $(this);
        var optionId = event.params.data.id;
        var $option = $select.find('> option[value="' + optionId + '"]');
        $option.detach();
        $select.append($option);
        $select.trigger('change');
    });

}(jQuery));
