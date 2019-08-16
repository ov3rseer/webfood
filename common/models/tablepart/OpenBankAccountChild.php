<?php

namespace common\models\tablepart;

use common\models\document\OpenBankAccount;
use common\models\reference\Child;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Комплексы (состав меню)" справочника "Меню"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $child_id
 * @property integer $snils
 * @property integer $codeword
 *
 * Отношения:
 * @property OpenBankAccount $parent
 * @property Child           $child
 */
class OpenBankAccountChild extends TablePart
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
        return $this->hasOne(OpenBankAccount::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getChild()
    {
        return $this->hasOne(Child::className(), ['id' => 'child_id']);
    }
}