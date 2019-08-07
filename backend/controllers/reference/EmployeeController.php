<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Сотрудники"
 */
class EmployeeController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Employee';
}