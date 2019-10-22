<?php

namespace frontend\models\serviceObject;

use common\models\ActiveRecord;
use common\models\form\SystemForm;
use common\models\reference\MealCategory;
use common\models\reference\ProductCategory;
use yii\helpers\Html;

/**
 * Базовая форма категорий
 */
abstract class CategoryForm extends SystemForm
{
    /**
     * @var string название категории
     */
    public $name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'filter', 'filter' => 'trim'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Наименование категории',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        $columns = [
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var MealCategory|ProductCategory $rowModel */
                    if ($rowModel->is_active == true) {
                        return 'Да';
                    } else {
                        return 'Нет';
                    }
                },
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($rowModel) {
                    /** @var MealCategory|ProductCategory $rowModel */
                    return Html::encode($rowModel);
                },
            ],
        ];
        return $columns;
    }
}