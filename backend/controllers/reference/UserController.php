<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Пользователи"
 */
class UserController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\User';
}
