<?php

namespace common\models\reference;

use common\models\tablepart\MenuComplex;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Комплекс"
 *
 * Отношения:
 * @property MenuComplex[] $menuComplexes
 */
class Menu extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Меню';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Меню';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'menuComplexes' => 'Комплексы (состав меню)',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getMenuComplexes()
    {
        return $this->hasMany(MenuComplex::className(), ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'menuComplexes' => MenuComplex::className(),
        ], parent::getTableParts());
    }
}