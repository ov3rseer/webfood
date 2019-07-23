<?php

namespace frontend\controllers\user;

use frontend\controllers\FrontendModelController;

/**
 * Контроллер для формы "Профиль"
 */
class ProfileController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\user\Profile';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\form\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/user/profile/index',
            ],
        ]);
    }
}