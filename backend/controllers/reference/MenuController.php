<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Меню"
 */
class MenuController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Menu';
}