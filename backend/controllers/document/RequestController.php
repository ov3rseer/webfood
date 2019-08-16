<?php

namespace backend\controllers\document;

/**
 * Контроллер для документов "Заявки"
 */
class RequestController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\Request';
}