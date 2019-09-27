<?php

namespace common\models\reference;

use backend\controllers\reference\ReferenceController;
use backend\widgets\ActiveForm;
use common\models\enum\ComplexType;
use common\models\tablepart\ComplexMeal;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Модель справочника "Комплекс"
 *
 * Свойства:
 * @property integer $complex_type_id
 *
 * Отношения:
 * @property ComplexType $complexType
 * @property ComplexMeal[] $complexMeal
 */
class Complex extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Комплекс';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Комплексы';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['complex_type_id'], 'integer'],
            [['complex_type_id'], 'required'],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'complex_type_id' => 'Тип комплекса',
            'complexMeals' => 'Блюда (состав комплекса)',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getComplexType()
    {
        return $this->hasOne(ComplexType::class, ['id' => 'complex_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getComplexMeals()
    {
        return $this->hasMany(ComplexMeal::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'complexMeals' => ComplexMeal::className(),
        ], parent::getTableParts());
    }

    /**
     * @inheritdoc
     * @param $tablePartRelation
     * @param $form
     * @param bool $readonly
     * @return array
     * @throws \ReflectionException
     * @throws InvalidConfigException
     */
    public function getTablePartColumns($tablePartRelation, $form, $readonly = false)
    {
        /** @var ActiveForm $form */
        $model = $this;
        $parentResult = ReferenceController::getTablePartColumns($model, $tablePartRelation, $form, $readonly);
        if ($tablePartRelation == 'complexMeals') {
            // Колонка продукты
            $parentResult['price'] = [
                'format' => 'raw',
                'label' => 'Цена',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) use ($form, $model, $tablePartRelation) {
                    /** @var ComplexMeal $rowModel */
                    $result = '';
                    if (!$rowModel->isNewRecord && isset($rowModel->meal->price)) {
                        $result = Html::textInput(
                            Html::getInputName($model, '[' . $tablePartRelation . '][' . $rowModel->id . ']price'),
                            Html::encode($rowModel->meal->price),
                            [
                                'id' => Html::getInputId($model, '[' . $tablePartRelation . '][' . $rowModel->id . ']price'),
                                'class' => 'form-control',
                                'readonly' => true
                            ]);
                    }
                    return $result;
                }
            ];
        }
        return $parentResult;
    }
}