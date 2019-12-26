<?php

namespace frontend\controllers\serviceObject;

use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use yii\filters\AccessControl;

class SetMenuController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClassForm = 'frontend\models\serviceObject\SetMenuForm';

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
                        'actions' => ['index', 'render-calendar', 'delete-menu', 'edit-weekend'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\base\IndexAction',
                'modelClassForm' => $this->modelClassForm,
                'viewPath' => '@frontend/views/service-object/set-menu/index',
            ],
            'delete-menu' => [
                'class' => 'frontend\actions\setMenu\DeleteMenuAction',
                'modelClassForm' => $this->modelClassForm,
            ],
            'edit-menu' => [
                'class' => 'frontend\actions\setMenu\EditMenuCalendar',
                'modelClassForm' => $this->modelClassForm,
            ],
            'render-calendar' => [
                'class' => 'frontend\actions\setMenu\RenderCalendarAction',
                'modelClassForm' => $this->modelClassForm,
            ],
        ]);
    }
}