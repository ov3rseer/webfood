<?php

namespace backend\controllers\document;

use backend\controllers\ModelController;
use common\models\ActiveRecord;
use common\models\register\registerAccumulate\RegisterAccumulate;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Базовый класс контроллера для моделей документов
 */
abstract class DocumentController extends ModelController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'create' => [
                'class' => 'backend\actions\base\CreateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/base/update',
            ],
            'update' => [
                'class' => 'backend\actions\base\UpdateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/base/update',
            ],
            'view' => [
                'class' => 'backend\actions\base\ViewAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/document/base/view',
            ],
            'search' => [
                'class' => 'backend\actions\base\SearchAction',
                'modelClass' => $this->modelClass,
                'searchFields' => ['id'],
            ],
            'restore' => [
                'class' => 'backend\actions\base\RestoreAction',
                'modelClass' => $this->modelClass,
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
                'rules' => [
                    [
                        'actions' => ['restore'],
                        'allow' => true,
                        'roles' => [static::className() . '.Restore'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Подготовка массива ошибок регистров для отображения в форме документа
     * @param array $registerErrors
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \ReflectionException
     */
    static function prepareRegistersErrors($registerErrors)
    {
        /** @var RegisterAccumulate[] $registerModels */
        $registerModels = [];
        $idsByClass = [];
        foreach ($registerErrors as $registerClass => $rows) {
            $registerModel = new $registerClass();
            $registerModels[$registerClass] = $registerModel;
            if (!$rows) {
                unset($registerErrors[$registerClass]);
                continue;
            }
            $attributesWithRelations = $registerModels[$registerClass]->getAttributesWithRelation();
            foreach($rows as $row) {
                foreach ($row as $field => $value) {
                    if (isset($attributesWithRelations[$field]) && $value) {
                        $idsByClass[$attributesWithRelations[$field]['class']][$value] = $value;
                    }
                }
            }
        }
        $namesByClass = [];
        foreach ($idsByClass as $className => $ids) {
            /** @var ActiveRecord $className */
            $records = $className::find()->andWhere(['id' => $ids])->indexBy('id')->all();
            foreach ($records as $id => $record) {
                $namesByClass[$className][$id] = (string)$record;
            }
        }
        $result = [];
        foreach ($registerErrors as $registerClass => $rows) {
            $registerModel = $registerModels[$registerClass];
            $attributesLabels = $registerModel->attributeLabels();
            $columns = [];
            $fields = array_keys($rows[0]);
            foreach ($fields as $field) {
                $column = [
                    'label' => isset($attributesLabels[$field]) ? $attributesLabels[$field] : $field,
                    'attribute' => $field,
                ];
                if (in_array($field, $registerModel->getResources())) {
                    $column['contentOptions'] = ['style' => 'text-align:right; color:#da4f49; font-weight:bold;'];
                    $column['format'] = 'decimal';
                }
                $columns[$field] = $column;
            }
            $attributesWithRelations = $registerModel->getAttributesWithRelation();
            $newRows = [];
            foreach ($rows as $row) {
                $newRow = [];
                foreach ($row as $field => $value) {
                    if (isset($attributesWithRelations[$field]) && $value) {
                        $newRow[$field] = $namesByClass[$attributesWithRelations[$field]['class']][$value];
                    } else {
                        $newRow[$field] = $value;
                    }
                }
                $newRows[] = $newRow;
            }
            $result[$registerClass] = [
                'label' => $registerModel->getPluralName(),
                'columns' => $columns,
                'dataProvider' => new ArrayDataProvider(['allModels' => $newRows]),
            ];
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function generateAutoColumns($model, $filterModel)
    {
        $result = parent::generateAutoColumns($model, $filterModel);
        unset($result['document_basis_id']);
        unset($result['document_basis_type_id']);
        unset($result['create_date']);
        unset($result['update_date']);
        $result = array_merge([
            [
                'attribute' => 'id',
                'label' => 'Номер',
                'headerOptions' => [
                    'style' => 'width: 100px;',
                ],
            ]
        ], $result);
        return $result;
    }
}