<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Объекты обслуживания"
 */
class ServiceObjectController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\ServiceObject';
}