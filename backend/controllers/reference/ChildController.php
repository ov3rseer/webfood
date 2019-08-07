<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Дети"
 */
class ChildController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Child';
}