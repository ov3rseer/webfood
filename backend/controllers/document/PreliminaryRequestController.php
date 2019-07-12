<?php


namespace backend\controllers\document;

/**
 * Контроллер для документов "Предварительные заявки"
 */
class PreliminaryRequestController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\PreliminaryRequest';
}