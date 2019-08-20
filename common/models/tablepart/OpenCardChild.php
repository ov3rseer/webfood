<?php

namespace common\models\tablepart;

use common\models\document\OpenCard;
use common\models\reference\Child;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Данные детей" документа "Открытие карт"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $child_id
 * @property integer $snils
 * @property integer $codeword
 *
 * Отношения:
 * @property OpenCard $parent
 * @property Child    $child
 */
class OpenCardChild extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['child_id'], 'integer'],
            [['child_id', 'snils', 'codeword'], 'required'],
            [['snils', 'codeword'], 'string']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'child_id'  => 'Ребёнок',
            'snils'     => 'СНИЛС',
            'codeword'  => 'Кодовое слово',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(OpenCard::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getChild()
    {
        return $this->hasOne(Child::className(), ['id' => 'child_id']);
    }
}