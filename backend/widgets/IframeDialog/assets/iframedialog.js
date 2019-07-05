;(function($) {

    'use strict';

    /**
     * Класс для диалогового окна с iframe
     * @param element
     * @param options массив опций
     * @constructor
     */
    var IframeDialog = function(element, options) {
        this.options = options;
        this.$element = $(element);
        this.$body = $(document.body);
        if (!this.options.url) {
            this.$element.remove();
            return;
        }
        if (!this.$element.attr('id')) {
            this.$element.attr('id', this.id());
        }
        if (!this.$element.attr('name')) {
            this.$element.attr('name', this.id());
        }
        if (!this.$element.attr('role')) {
            this.$element.attr('role', 'dialog');
        }
        this.$element.addClass('modal');
        this.$element.addClass('fade');
        var dialog =
            '<div class="modal-dialog modal-lg">' +
                '<div class="modal-content">' +
                    '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
                        '<h4 class="modal-title" id="myModalLabel"></h4>' +
                    '</div>' +
                    '<iframe src="' + this.options.url + '"></iframe>' +
                '</div>' +
            '</div>';
        var id = this.id();
        var that = this;
        this.$element.append(dialog).find('iframe').on('load', function() {
            $(this).contents().find('body').attr('data-parent-dialog-id', id);
            that.$element.find('.modal-title').html($(this).contents().find('title').text());
        });
        if (this.options.events) {
            $(this.options.events).each(function(index, element) {
                that.$element.on(element.eventType, element.callback);
            });
        }
        this.$element.on('hidden.bs.modal', function () {
            that.destroy();
        });
        this.$element.appendTo(this.$body[0]);
        this.$element.modal({ show: this.options.show });
    };

    /**
     * Значения по умолчанию
     * @type {{url: boolean, show: boolean, opener: null, events: [{eventType: 'hide.bs.modal', callback: function (e)}]}}
     */
    IframeDialog.DEFAULTS = {
        url: false,
        show: true,
        opener: null,
        events: {}
    };

    /**
     * Метод для генерации id экземпляра
     * @returns {string}
     */
    IframeDialog.prototype.id = function() {
        if (!this.options.id) {
            var chars = "0123456789abcdefghiklmnopqrstuvwxyz";
            var string_length = 13;
            var randomstring = '';
            for (var x = 0; x < string_length; x++) {
                var letterOrNumber = Math.floor(Math.random() * 2);
                if (letterOrNumber == 0) {
                    randomstring += Math.floor(Math.random() * 9);
                } else {
                    var rnum = Math.floor(Math.random() * chars.length);
                    randomstring += chars.substring(rnum, rnum + 1);
                }
            }
            this.options.id = 'dialog_' + randomstring;
        }
        return this.options.id;
    };

    /**
     * Метод для отображения диалогового окна
     */
    IframeDialog.prototype.show = function() {
        this.$element.modal('show');
    };

    /**
     * Метод для скрытия диалогового окна
     */
    IframeDialog.prototype.hide = function() {
        this.$element.modal('hide');
    };

    /**
     * Возвращает значения опций
     * @param {string} name название опции
     * @returns {*}
     */
    IframeDialog.prototype.option = function(name) {
        return this.options[name];
    };

    /**
     * Деструктор класса
     */
    IframeDialog.prototype.destroy = function() {
        this.$element.remove();
    };
    
    function Plugin(option, _relatedTarget) {
        var result;
        this.each(function () {
            var data = $(this).data('ct.modal'),
                options = $.extend({}, IframeDialog.DEFAULTS, $(this).data(), typeof option == 'object' && option);
            if (!data) {
                $(this).data('ct.modal', (data = new IframeDialog(this, options)));
            }
            if (typeof option == 'string') {
                result = data[option](_relatedTarget);
            }
        });
        if (result) {
            return result;
        } else {
            return this;
        }
    }

    $.fn.iframedialog = Plugin;
    $.fn.iframedialog.Constructor = IframeDialog;

    /**
     * Функция определения родительского окна для любого элемента
     * @returns {*}
     */
    $.fn.ownerDialog = function() {
        if (this[0].ownerDocument != top.document) {
            return top.$('#' + $(this[0].ownerDocument.body).attr('data-parent-dialog-id'));
        }
        return undefined;
    };

    /**
     * Возвращает элемент в контексте родительского окна
     * @returns {*}
     */
    $.fn.getInOwnContext = function() {
        var ownerDialog = $(this).ownerDialog();
        if (typeof ownerDialog != 'undefined') {
            return top[ownerDialog.attr('name')].getElementsByTagName('iframe')[0].contentWindow.$(this);
        }
        return this;
    };

    /**
     * Псевдоним для функции Jquery без селектора
     * @param options
     * @returns {*}
     */
    $.iframedialog = function(options) {
        //noinspection JSUnresolvedVariable
        if (typeof options.id != 'undefined' && window.top.$('#' + options.id).length != 0) {
            return null;
        } else {
            //noinspection JSUnresolvedFunction
            return window.top.$('<div class="iframe-dialog"></div>').iframedialog(options);
        }
    };

    $(document).on('click', '.iframe-open', function (e) {
        e.preventDefault();
        var href = new URL($(this).attr('href'), document.location.href);
        href.search += "&layout=iframe";
        $.iframedialog({
            url: href.toString(),
            opener: $(this)
        });
    });

}(jQuery));
