<?php

namespace common\models\document;

use common\components\DateTime;
use common\models\reference\Menu;
use yii\db\ActiveQuery;

/**
 * Модель документа "Установка меню"
 *
 * Свойства:
 * @property integer $menu_id
 * @property float   $day
 *
 * Отношения:
 * @property Menu $card
 */
class SetMenu extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Установка меню';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Установки меню';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['menu_id'], 'integer'],
            [['day'], 'date', 'format' => 'php:' . DateTime::DB_DATE_FORMAT],
            [['menu_id', 'day'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'menu_id'   => 'Меню',
            'day'       => 'День',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getMenu()
    {
        return $this->hasOne(Menu::class, ['id' => 'menu_id']);
    }
}