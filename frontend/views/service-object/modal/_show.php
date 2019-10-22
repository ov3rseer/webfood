<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;

$updateButtonId = 'update-button';

echo $data;

Modal::begin([
    'id' => 'events',
    'header' => '<h2>Изменить категорию</h2>',
    'footer' => Html::button('Изменить', [
        'id' => $updateButtonId,
        'class' => 'btn btn-success',
    ]),
]);
echo Html::tag('span', '', ['id' => 'modelContent']);
//echo Html::beginTag('div', ['class' => 'row']);
//echo Html::beginTag('div', ['class' => 'col-xs-12']);
//echo Html::label('Наименование категории', 'category-name-' . $id);
//echo Html::textInput('category_name', Html::encode($category_name), [
//    'id' => 'category-name-' . $id,
//    'class' => 'form-control',
//]);
//echo Html::endTag('div');
//echo Html::endTag('div');
//echo Html::beginTag('div', ['class' => 'row mt-3']);
//echo Html::beginTag('div', ['class' => 'col-xs-12']);
//echo Html::checkbox('is_active', $is_active, ['id' => 'active-' . $id, 'label' => 'Активен']);
//echo Html::endTag('div');
//echo Html::endTag('div');
Modal::end();

//$this->registerJs("
//    $('#" . $updateButtonId . "').click(function(e){
//        $('#" . $modalId . "').modal('hide');
//        e.preventDefault();
//        var id = $(this).data('id');
//        var is_active = $('#active-" . $id . "').is(':checked');
//        var category_name = $('#category-name-" . $id . "').val();
//        $.ajax({
//            url: 'update',
//            data: {id: id, is_active: is_active, category_name: category_name},
//            dataType: 'json',
//            type: 'POST',
//            success: function(data) {
//                $.pjax.reload('#" . $pjaxId . "', {
//                    replace: true,
//                    timeout: 5000,
//                });
//            }
//        });
//    });
//");