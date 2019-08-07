<?php

namespace common\models\tablepart;

use common\models\reference\Child;
use common\models\reference\Father;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Ребенок" справочника "Родители"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $child_id
 *
 * Отношения:
 * @property Father    $parent
 * @property Child     $child
 */
class FatherChild extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['child_id'], 'integer'],
            [['child_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'child_id' => 'Ребенок',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Father::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getChild()
    {
        return $this->hasOne(Child::className(), ['id' => 'child_id']);
    }
}