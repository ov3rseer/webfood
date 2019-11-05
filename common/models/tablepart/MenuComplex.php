<?php

namespace common\models\tablepart;

use common\models\reference\Complex;
use common\models\reference\Menu;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Комплексы" справочника "Меню"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $complex_id
 * @property integer $quantity
 *
 * Отношения:
 * @property Menu       $parent
 * @property Complex    $complex
 */
class MenuComplex extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['complex_id', 'quantity'], 'integer'],
            [['complex_id', 'quantity'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'complex_id' => 'Комплекс',
            'quantity' => 'Количество',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Menu::class, ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getComplex()
    {
        return $this->hasOne(Complex::class, ['id' => 'complex_id']);
    }
}