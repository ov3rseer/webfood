<?php

namespace backend\controllers\reference;

use backend\controllers\BackendModelController;

/**
 * Контроллер для справочника "Карты детей"
 */
class CardChildController extends BackendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\CardChild';
}