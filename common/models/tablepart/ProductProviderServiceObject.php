<?php

namespace common\models\tablepart;

use common\models\reference\ProductProvider;
use common\models\reference\ServiceObject;
use yii\db\ActiveQuery;

/**
 * Модель строки табличной части "Объект обслуживания" справочника "Поставщик продуктов"
 *
 * Свойства:
 * @property integer $parent_id
 * @property integer $service_object_id
 *
 * Отношения:
 * @property ProductProvider  $parent
 * @property ServiceObject    $serviceObject
 */
class ProductProviderServiceObject extends TablePart
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object_id'], 'integer'],
            [['service_object_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'service_object_id' => 'Объект обслуживания',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(ProductProvider::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceObject()
    {
        return $this->hasOne(ServiceObject::className(), ['id' => 'service_object_id']);
    }
}