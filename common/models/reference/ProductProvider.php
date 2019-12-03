<?php


namespace common\models\reference;

use backend\widgets\ActiveField;
use common\models\tablepart\ProductProviderServiceObject;
use yii\db\ActiveQuery;

/**
 * Модель справочника "Поставщик продуктов"
 *
 * @property integer $user_id
 * @property string $city
 * @property string $address
 *
 * Отношения:
 * @property User $user
 * @property ProductProviderServiceObject[] $productProviderServiceObjects
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
            [['city', 'address'], 'string'],
            [['city', 'address'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'user_id' => 'Прикреплённый пользователь',
            'city' => 'Город',
            'address' => 'Адрес',
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
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            if ($this->scenario != self::SCENARIO_SEARCH) {
                if ($this->user_id) {
                    $this->_fieldsOptions['user_id']['displayType'] = ActiveField::READONLY;
                } else {
                    $this->_fieldsOptions['user_id']['displayType'] = ActiveField::REFERENCE;
                }
            }
        }
        return $this->_fieldsOptions;
    }

    /**
     * @inheritdoc
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            if ($this->user_id) {
                $this->is_active = true;
            } else {
                $this->is_active = false;
            }
        }
        return $parentResult;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->user) {
            $this->user->name_full = $this->name;
            $this->user->save();
        }
    }
}