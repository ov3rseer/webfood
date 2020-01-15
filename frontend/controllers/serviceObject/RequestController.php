<?php

namespace frontend\controllers\serviceObject;

use common\helpers\ArrayHelper;
use common\models\document\Request;
use frontend\controllers\FrontendModelController;
use yii\filters\AccessControl;

/**
 * Контроллер для форм "Заявки"
 */
class RequestController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClassForm = 'frontend\models\serviceObject\RequestForm';

    /**
     * @var string имя класса модели
     */
    public $modelClass= 'common\models\document\Request';

    /**
     * @var string путь к файлу представления для вкладок
     */
    public $tabsViewPath;

    /**
     * @var string путь к файлу представления
     */
    public $viewPath = '@frontend/views/service-object/request/update';


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\request\IndexAction',
                'modelClass' => $this->modelClass,
                'modelClassForm' => $this->modelClassForm,
                'viewPath' => '@frontend/views/service-object/request/index',
            ],
            'create' => [
                'class' => 'frontend\actions\request\CreateAction',
                'modelClass' => $this->modelClass,
                'modelClassForm' => $this->modelClassForm,
            ],
            'update' => [
                'class' => 'frontend\actions\base\UpdateAction',
                'modelClass' => $this->modelClass,
                'modelClassForm' => $this->modelClassForm,
                'viewPath' => '@frontend/views/service-object/request/update',
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
                        'actions' => ['index', 'update', 'create'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     * @param Request $model
     */
    static public function getTablePartColumns($model, $tablePartRelation, $form, $readonly = false)
    {
        return $model->getTablePartColumns($tablePartRelation, $form, $readonly);
    }
}