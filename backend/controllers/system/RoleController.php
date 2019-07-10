<?php

namespace backend\controllers\system;

use backend\widgets\ActiveForm;
use common\components\DbManager;
use common\helpers\ArrayHelper;
use common\models\system\Role;
use common\queries\ActiveQuery;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Контроллер для управления операциями
 */
class RoleController extends SystemController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'common\models\system\Role';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [];
    }

    /**
     * Просмотр и фильтрация журнала
     * @return string
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        /** @var Role $modelClass */
        $modelClass = $this->modelClass;
        /** @var Role $filterModel */
        $filterModel = new $modelClass(['scenario' => $modelClass::SCENARIO_SEARCH]);
        /** @var ActiveDataProvider $dp */
        $dp = $filterModel->search(Yii::$app->request->get());
        /** @var ActiveQuery $query */
        $query = $dp->query;
        $query->andWhere('name != :name', [':name' => DbManager::ADMIN_ROLE]);
        return $this->renderUniversal('index', [
            'filterModel'  => $filterModel,
            'dataProvider' => $dp
        ]);
    }

    /**
     * Редактирование элемента
     * @param $id
     * @return array|string
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UserException
     */
    public function actionUpdate($id)
    {
        /** @var Role $modelClass */
        $modelClass = $this->modelClass;
        /** @var Role $model */
        $model = $modelClass::findOne($id);
        if (Yii::$app->request->isAjax) {
            $model->load(Yii::$app->request->post());
            if (!Yii::$app->request->isPjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                return $this->renderUniversal($this->viewPath, ['model' => $model]);
            }
        } else if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            Yii::$app->session->setFlash('success', 'Элемент "' . $model . '" успешно сохранен');
            return $this->autoRedirect(['update', 'id' => $model->name]);
        }
        return $this->renderUniversal('update', ['model' => $model]);
    }

    /**
     * Удаление элемента
     * @param $id
     * @return array|string
     * @throws HttpException
     */
    public function actionDelete($id)
    {
        $role = Yii::$app->authManager->getRole($id);
        if (!$role) {
            throw new HttpException(404);
        }
        Yii::$app->authManager->remove($role);
        return $this->autoRedirect(['index']);
    }

    /**
     * Создание элемента
     * @return array|string
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UserException
     */
    public function actionCreate()
    {
        /** @var Role $modelClass */
        $modelClass = $this->modelClass;
        /** @var Role $model */
        $model = new $modelClass();
        if (Yii::$app->request->isAjax) {
            $model->load(Yii::$app->request->post());
            if (!Yii::$app->request->isPjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                return $this->renderUniversal($this->viewPath, ['model' => $model]);
            }
        } else if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            return $this->autoRedirect(['update', 'id' => $model->name]);
        }
        return $this->renderUniversal('update', ['model' => $model]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow'   => true,
                        'roles'   => [static::className() . '.Index'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow'   => true,
                        'roles'   => [static::className() . '.Update', static::className() . '.View'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow'   => true,
                        'roles'   => [static::className() . '.Create'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow'   => true,
                        'roles'   => [static::className() . '.Delete'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Получение списка вкладок для формы редактирования
     * @param Role $model
     * @return array
     */
    public function getTabs($model)
    {
        $result = [];
        $columns = [
            'reference' => [
                [
                    'attribute' => 'name',
                    'label'     => 'Операция',
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Index'],
                            [
                                'value' => $data['id'] . '.Index',
                                'class' => 'assigned-reference-index'
                            ]
                        );
                    },
                    'header' => 'Список<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-reference-index-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['View'],
                            [
                                'value' => $data['id'] . '.View',
                                'class' => 'assigned-reference-view'
                            ]
                        );
                    },
                    'header' => 'Просмотр<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-reference-view-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Create'],
                            [
                                'value' => $data['id'] . '.Create',
                                'class' => 'assigned-reference-create'
                            ]
                        );
                    },
                    'header' => 'Создать<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-reference-create-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Update'],
                            [
                                'value' => $data['id'] . '.Update',
                                'class' => 'assigned-reference-update'
                            ]
                        );
                    },
                    'header' => 'Изменить<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-reference-update-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Delete'],
                            [
                                'value' => $data['id'] . '.Delete',
                                'class' => 'assigned-reference-delete'
                            ]
                        );
                    },
                    'header' => 'Удалить<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-reference-delete-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Restore'],
                            [
                                'value' => $data['id'] . '.Restore',
                                'class' => 'assigned-reference-restore'
                            ]
                        );
                    },
                    'header' => 'Восстановить<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-reference-restore-all']
                        )
                ],
            ],
            'document' => [
                [
                    'attribute' => 'name',
                    'label'     => 'Операция',
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Index'],
                            [
                                'value' => $data['id'] . '.Index',
                                'class' => 'assigned-document-index'
                            ]
                        );
                    },
                    'header' => 'Список<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-document-index-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['View'],
                            [
                                'value' => $data['id'] . '.View',
                                'class' => 'assigned-document-view'
                            ]
                        );
                    },
                    'header' => 'Просмотр<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-document-view-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Create'],
                            [
                                'value' => $data['id'] . '.Create',
                                'class' => 'assigned-document-create'
                            ]
                        );
                    },
                    'header' => 'Создать<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-document-create-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Update'],
                            [
                                'value' => $data['id'] . '.Update',
                                'class' => 'assigned-document-update'
                            ]
                        );
                    },
                    'header' => 'Изменить<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-document-update-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Delete'],
                            [
                                'value' => $data['id'] . '.Delete',
                                'class' => 'assigned-document-delete'
                            ]
                        );
                    },
                    'header' => 'Удалить<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-document-delete-all']
                        )
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['Restore'],
                            [
                                'value' => $data['id'] . '.Restore',
                                'class' => 'assigned-reference-restore'
                            ]
                        );
                    },
                    'header' => 'Восстановить<br />'
                        . Html::checkbox(
                            Html::getInputName($model, 'assigned_all[]'),
                            false,
                            ['class' => 'assigned-reference-restore-all']
                        )
                ],
            ],
            'other' => [
                [
                    'attribute' => 'name',
                    'label'     => 'Операция',
                ],
                [
                    'class'     => DataColumn::className(),
                    'format'  => 'raw',
                    'value' => function ($data) use ($model) {
                        return Html::checkbox(
                            Html::getInputName($model, 'assigned[]'),
                            $data['isAssigned'],
                            [
                                'value' => $data['id'],
                                'class' => 'assigned-other'
                            ]
                        );
                    },
                    'header' => Html::checkbox(
                        Html::getInputName($model, 'assigned_all[]'),
                        false,
                        ['class' => 'assigned-other-all']
                    )
                ],
            ],
        ];
        $names = [
            'reference' => 'Справочники',
            'document'  => 'Документы',
            'other'     => 'Разное',
        ];
        foreach ($model->getPermissionDataProviders() as $section => $dataProvider) {
            $result[$section] = [
                'label' => $names[$section],
                'view' => '@backend/views/system/role/_tablePart',
                'params' => [
                    'dataProvider' => $dataProvider,
                    'columns'      => $columns[$section],
                    'section'      => $section,
                ],
            ];
        }
        return $result;
    }
}