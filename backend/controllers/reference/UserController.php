<?php

namespace backend\controllers\reference;

use common\helpers\ArrayHelper;

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
        $result = parent::actions();
        if (isset($result['create'])) {
            unset($result['create']);
        }
        return array_merge($result, [
            'update' => [
                'class' => 'backend\actions\base\UpdateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/user/update',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function generateAutoColumns($model, $filterModel)
    {
        $result = ReferenceController::generateAutoColumns($model, $filterModel);
        return ArrayHelper::filter($result, ['name', 'name_full', 'is_active', 'userType', 'email', 'createUser', 'updateUser', 'create_date', 'update_date']);
    }
}
