<?php


namespace backend\controllers\document;

/**
 * Контроллер для документов "Корректировки заявок"
 */
class CorrectionRequestController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\CorrectionRequest';
}