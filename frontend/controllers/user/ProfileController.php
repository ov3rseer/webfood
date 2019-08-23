<?php

namespace frontend\controllers\user;

use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use yii\filters\AccessControl;

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
                'class' => 'frontend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/user/profile/index',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['super-admin', 'service-object', 'other', 'employee', 'father', 'product-provider'],
                    ],
                ],
            ],
        ]);
    }
}