<?php

namespace backend\controllers\document;

use common\helpers\ArrayHelper;
use common\models\document\Request;
use yii\filters\AccessControl;

/**
 * Контроллер для документов "Заявки"
 */
class RequestController extends DocumentController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\document\Request';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'export-request' => [
                'class' => 'backend\actions\document\request\ExportRequestAction',
                'modelClass' => $this->modelClass,
            ],
            'update' => [
                'class' => 'backend\actions\document\base\UpdateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/document/request/update',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['export-request'],
                        'allow' => true,
                        'roles' => [Request::class . '.Update'],
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