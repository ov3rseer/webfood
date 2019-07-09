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

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'create' => [
                'class' => 'backend\actions\base\CreateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/user/update',
            ],
            'update' => [
                'class' => 'backend\actions\base\UpdateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/user/update',
            ],
        ]);
    }
}
