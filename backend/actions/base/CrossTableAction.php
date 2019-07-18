<?php

namespace backend\actions\base;

use backend\actions\BackendModelAction;
use backend\controllers\BackendModelController;
use backend\widgets\ActiveForm;
use common\components\DateTime;
use common\helpers\StringHelper;
use common\models\ActiveRecord;
use common\models\cross\CrossTable;
use common\models\document\Document;
use common\models\reference\Reference;
use yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\web\Response;

/**
 * Действие для вывода/сохранения связанных записей из кросс-таблицы
 */
class CrossTableAction extends BackendModelAction
{
    /**
     * @var BackendModelController
     */
    public $controller;

    /**
     * @var string имя отношения
     */
    public $relationName;

    /**
     * @var string класс отношения
     */
    public $relationClass;

    /**
     * @var string поле со ссылкой на родительский объект
     */
    public $parentAttribute;

    /**
     * @var string путь к файлу представления для отображения вкладок
     */
    public $tabsViewPath;

    /**
     * @var boolean возможность редактирования строк
     */
    public $isEditable = true;

    /**
     * @var boolean записи будут редактироваться на отдельных страницах
     */
    public $isExternalEdit = false;

    /**
     * @var string URL для создания новой записи (для $isExternalEdit = true)
     */
    public $createUrl;

    /**
     * @var string URL для редактирования записи (для $isExternalEdit = true)
     */
    public $updateUrl;

    /**
     * @inheritdoc
     * @param $id
     * @return array|string|Response
     * @throws \ReflectionException
     * @throws \Throwable
     * @throws yii\base\InvalidConfigException
     * @throws yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /** @var ActiveRecord $model */
        $model = $this->controller->findModel($id);
        /** @var ActiveRecord $modelClass */
        $modelClass = $this->relationClass;
        /** @var ActiveRecord $filterModel */
        $filterModel = new $modelClass(['scenario' => $modelClass::SCENARIO_SEARCH]);
        if ($this->isEditable && !$this->isExternalEdit) {
            if (Yii::$app->request->isAjax) {
                if (!Yii::$app->request->isPjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $this->loadRelatedRecords($model);
                    return ActiveForm::validateMultiple($model->{$this->relationName});
                } else {
                    if (!Yii::$app->request->post('refresh', false)) {
                        $this->loadRelatedRecords($model);
                    }
                    if (Yii::$app->request->post('addRow', false)) {
                        Model::validateMultiple($model->{$this->relationName});
                        $this->addRelatedRecord($model);
                    }
                    $dataProvider = new ArrayDataProvider([
                        'allModels' => $model->{$this->relationName},
                        'pagination' => false,
                    ]);
                    return $this->controller->renderUniversal($this->viewPath, [
                        'model' => $model,
                        'filterModel'  => $filterModel,
                        'dataProvider' => $dataProvider,
                    ]);
                }
            } else {
                if (Yii::$app->request->isPost) {
                    $this->loadRelatedRecords($model);
                    if (Model::validateMultiple($model->{$this->relationName})) {
                        $transaction = Yii::$app->db->beginTransaction();
                        try {
                            /** @var CrossTable[] $existentRows */
                            $existentRows = $model->{'get' . ucfirst($this->relationName)}()->indexBy('id')->all();
                            /** @var CrossTable[] $relatedRecords */
                            $relatedRecords = $model->{$this->relationName};
                            foreach ($relatedRecords as $row) {
                                if (isset($existentRows[$row->id])) {
                                    unset($existentRows[$row->id]);
                                } else {
                                    unset($row->id);
                                }
                                if ($this->parentAttribute) {
                                    $row->{$this->parentAttribute} = $model->id;
                                }
                                $row->save();
                            }
                            foreach ($existentRows as $existentRow) {
                                $existentRow->delete();
                            }
                            if ($model instanceof Reference || $model instanceof Document) {
                                // Сохраняем основную модель для обновления даты изменения
                                $model->scenario = ActiveRecord::SCENARIO_SYSTEM;
                                $model->update_date = new DateTime('now');
                                $model->save();
                            }
                            $transaction->commit();
                        } catch (\Exception $ex) {
                            $transaction->rollBack();
                            throw $ex;
                        }
                        return $this->controller->autoRedirect(['', 'id' => $model->id]);
                    }
                }
            }
            $dataProvider = new ArrayDataProvider([
                'allModels' => $model->{$this->relationName},
                'pagination' => false,
            ]);
        } else {
            $dataProvider = $filterModel->search(\Yii::$app->request->get(), $model->{'get' . ucfirst($this->relationName)}());
        }
        return $this->controller->renderUniversal($this->viewPath, [
            'model' => $model,
            'filterModel'  => $filterModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Загрузка текущих записей
     * @param ActiveRecord $model
     * @return boolean
     * @throws yii\base\InvalidConfigException
     */
    public function loadRelatedRecords($model)
    {
        /** @var CrossTable $relationClass */
        $relationClass = $this->relationClass;
        /** @var CrossTable $newRecord */
        $newRecord = new $relationClass();
        $formName = $newRecord->formName();
        $rows = [];
        foreach (\Yii::$app->request->post($formName, []) as $rowId => $rowData) {
            $row = is_integer($rowId) ? $relationClass::findOne($rowId) : new $relationClass();
            if ($this->parentAttribute) {
                $row->{$this->parentAttribute} = $model->id;
            }
            $row->id = $rowId;
            $rows[$row->id] = $row;
        }
        $result = Model::loadMultiple($rows, \Yii::$app->request->post());
        $model->populateRelation($this->relationName, $rows);
        return $result;
    }

    /**
     * Добавление новой записи
     * @param ActiveRecord $model
     */
    protected function addRelatedRecord($model)
    {
        $rows = $model->{$this->relationName};
        $row = new $this->relationClass();
        if ($this->parentAttribute) {
            $row->{$this->parentAttribute} = $model->id;
        }
        $row->id = StringHelper::generateFakeId();
        $rows[$row->id] = $row;
        $model->populateRelation($this->relationName, $rows);
    }
}
