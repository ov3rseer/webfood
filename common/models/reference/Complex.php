<?php

namespace common\models\reference;

use common\models\enum\ComplexType;
use common\models\tablepart\ComplexMeal;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Комплекс"
 *
 * Свойства:
 * @property integer    $complex_type_id
 *
 * Отношения:
 * @property ComplexType   $complexType
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
            'complex_type_id'   => 'Тип комплекса',
            'complexMeals'      => 'Блюда (состав комплекса)',
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
}