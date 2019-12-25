<?php

namespace common\models\document;

use common\components\DateTime;
use common\models\enum\RequestStatus;
use common\models\reference\ProductProvider;
use common\models\reference\ServiceObject;
use common\models\tablepart\RequestProduct;
use yii\db\ActiveQuery;

/**
 * Модель документа "Предварительная заявка"
 *
 * @property integer $service_object_id
 * @property integer $product_provider_id
 * @property integer $request_status_id
 * @property DateTime $delivery_day
 *
 * Отношения:
 * @property ProductProvider $productProvider
 * @property ServiceObject $serviceObject
 * @property RequestStatus $requestStatus
 * @property RequestProduct[] $requestProducts
 */
class Request extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Заявка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Заявки';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['service_object_id', 'request_status_id', 'product_provider_id'], 'integer'],
            [['delivery_day'], 'date', 'format' => 'php:' . DateTime::DB_DATE_FORMAT],
            [['service_object_id', 'request_status_id', 'product_provider_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'service_object_id'     => 'Объект обслуживания',
            'product_provider_id'   => 'Поставщик продуктов',
            'request_status_id'     => 'Статус заявки',
            'delivery_day'          => 'Дата поставки',
            'requestProducts'       => 'Продукты',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getServiceObject()
    {
        return $this->hasOne(ServiceObject::class, ['id' => 'service_object_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductProvider()
    {
        return $this->hasOne(ProductProvider::class, ['id' => 'product_provider_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestStatus()
    {
        return $this->hasOne(RequestStatus::class, ['id' => 'request_status_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRequestProducts()
    {
        return $this->hasMany(RequestProduct::class, ['parent_id' => 'id'])->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        $result = parent::getTableParts();
        if ($this->product_provider_id) {
            $result = array_merge([
                'requestProducts' => RequestProduct::class,
            ], $result);
        }
        return $result;
    }
}
