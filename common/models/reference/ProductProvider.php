<?php


namespace common\models\reference;

use common\models\tablepart\ProductProviderServiceObject;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Поставщик продуктов"
 *
 * @property integer  $user_id
 *
 * Отношения:
 * @property User                               $user
 * @property ProductProviderServiceObject[]     $productProviderServiceObjects
 */
class ProductProvider extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Поставщик подуктов';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Поставщики продуктов';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['user_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'user_id'                       => 'Прикреплённый пользователь',
            'productProviderServiceObjects' => 'Объекты обслуживания',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductProviderServiceObjects()
    {
        return $this->hasMany(ProductProviderServiceObject::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'productProviderServiceObjects' => ProductProviderServiceObject::class,
        ], parent::getTableParts());
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult && $this->user_id) {
            $this->is_active = true;
        } else {
            $this->is_active = false;
        }
        return $parentResult;
    }
}