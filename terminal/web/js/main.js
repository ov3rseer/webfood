/*globals $:false */

$(function () {
    'use strict';

    eTerminalQtyButtons();
    sidebar_init();
});

function eTerminalQtyButtons() {
    // Обрабатываем нажатия на кнопки +- -
    // Обрабатываем нажатие на "переключатель" (js_e_qty_toggle) - элемент, при клике, на который товар либо добавляется, либо удаляется
    // Меняем количество в соответствующих инпутах и лейблах

    /*
    минимальная работающие элементы:

    инпут:
    <input type="number" name="<?=$value['id']?>">
    name должен быть равным id товара (или как минимум data-e_product_id его элемента), чтобы всё корректно работало

    кнопка для удаления товара:
    <sometag class="js_e_qty_remove" data-for="<?=$value['id']?>"></sometag>

    кнопка для добавления товара:
    <sometag class="js_e_qty_add" data-for="<?=$value['id']?>"></sometag>

    Все кнопки .js_e_qty_add с одинаковым data-for будут добавлять значение input'a с type="number" и соответствующим name, равным id товара

    Элемент, в котором отображается текст актуального количества товара в корзине
    <sometag class="js_e_qty" data-for="<?=$value['id']?>"></sometag>

    Если добавить элементу css-класс js_e_qty и добавить атрибут data-for равный id товара (точнее значение атрибута name у инпута, в котором должен быть id товара), то при изменении инпута, актуальное количество запишется в этот элемент
    */

    // Добавление товара - нажатие на "+"
    $(".js_e_qty_add").click(function () {
        // $(this).toggleClass("highlight");
        var id = $(this).attr("data-for");
        var input = $("input[type=number][name=" + id + "]");
        var qty = input.val();
        input.val(qty);
        input.each(function (index, element) {
            this.stepUp(1);
        });
        input.trigger("change");
    });

    // Уменьшение количества товара - нажатие на "-"
    $(".js_e_qty_remove").click(function () {
        var id = $(this).attr("data-for");
        var input = $("input[type=number][name=" + id + "]");
        var qty = input.val();
        input.val(qty);
        input.each(function (index, element) {
            this.stepDown(1);
        });
        input.trigger("change");
    });

    // "переключение" - если товар в корзине - удалить, и наоборот
    $(".js_e_qty_toggle").click(function () {
        var id = $(this).attr("data-for");
        var input = $("input[type=number][name=" + id + "]");
        var qty = input.val();
        if (qty > 0) {
            input.each(function (index, element) {
                var min = (input).attr('min');
                if (!min) {
                    min = 0;
                }
                $(this).val(min);
            });
        } else {
            input.each(function (index, element) {
                this.stepUp(1);
            });
        }
        input.trigger("change");
    });

    // Обрабатываем изменение количества товара
    $("input[type=number]").change(function () {
        // при изменении количества, если товар добавлен в корзину - добавляем css-класс active элементу с data-e_product_id равным id товара (id записан как name у инпута)
        var id = $(this).attr("name");
        var category = $(this).data("category");
        var price = $(this).data("price");
        var cards = $("*[data-e_product_id=" + id + "]");
        var qty_btn_remove = $('.js_e_qty_remove[data-for="' + id + '"]');
        var qty = $(this).val();

        if (qty > 0) {
            cards.addClass('active');
        } else {
            cards.removeClass('active');
        }

        // Если нажатие на минус приведёт к удалению товара - добавляем кнопке класс "remove"
        if (qty == 1) {
            qty_btn_remove.addClass('remove');
        } else {
            qty_btn_remove.removeClass('remove');
        }

        // Пишем актуальное значение в соответствующие элементы с классом js_e_qty и data-for равным значению name у соответствующего инпута (равно id товара)
        $('.js_e_qty[data-for="' + id + '"]').text(qty);

        $.ajax({
            url: location.origin + '/terminal/cart/cart-revision',
            data: {'qty': qty, 'id': id, 'category': category, 'price': price},
            dataType: 'json',
            type: 'POST',
        });
    });

    // Запускаем первый раз всё что написано выше
    $("input[type=number]").trigger("change");


    /*
    2DO
    т.к. инпутов для одного и того же товара может быть несколько то при
    $( "input[type=number]" ).change()
    будет одновременно вызываться столько раз, сколько инпутов
    */
}


function sidebar_init() {

    var sidebars_btn = document.querySelectorAll('.wf-js-sidebar-expand-btn');

    // Закрываем/открываем боковое меню по нажатию на кнопку
    for (var i = sidebars_btn.length - 1; i >= 0; i--) {
        sidebars_btn[i].addEventListener('click', function () {
            var sidebars = document.querySelectorAll('.left-sidebar');
            for (var i = sidebars.length - 1; i >= 0; i--) {
                sidebars[i].classList.toggle('expand');
            }
        });
    }

    // Закрываем боковое меню по нажатию на затемнённый фон (любое место мимо самого меню)

    var sidebar_close_btn = document.querySelectorAll('.wf-js-sidebar-close');

    // Закрываем/открываем боковое меню по нажатию на кнопку
    for (var i = sidebar_close_btn.length - 1; i >= 0; i--) {
        sidebar_close_btn[i].addEventListener('click', function () {
            var sidebars = document.querySelectorAll('.left-sidebar');
            for (var i = sidebars.length - 1; i >= 0; i--) {
                sidebars[i].classList.remove('expand');
            }
        });
    }
}

