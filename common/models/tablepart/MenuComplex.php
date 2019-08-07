<?php

namespace common\models\tablepart;

use common\models\reference\Complex;
use common\models\reference\Menu;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Комплексы (состав меню)" справочника "Меню"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $complex_id
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
            [['complex_id'], 'integer'],
            [['complex_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'complex_id' => 'Комплекс',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Menu::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getComplex()
    {
        return $this->hasOne(Complex::className(), ['id' => 'complex_id']);
    }
}