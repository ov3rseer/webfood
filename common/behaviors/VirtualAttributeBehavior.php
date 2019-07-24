<?php

namespace common\behaviors;

use common\components\DateTime;
use common\models\ActiveRecord;
use yii\base\Behavior;
use yii\helpers\Json;

/**
 * Поведение виртуальных параметров
 */
class VirtualAttributeBehavior extends Behavior
{
    /**
     * @var array список свойств модели, которые должны быть упакованы в JSON-массив
     */
    public $attributes = [];

    /**
     * @var string название поля в БД для хранения виртуальных свойств модели
     */
    public $parameterName = 'value';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'packAttributes',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'packAttributes',
            ActiveRecord::EVENT_AFTER_FIND    => 'unpackAttributes',
        ];
    }

    /**
     * Упаковка параметров модели в JSON-массив
     */
    public function packAttributes()
    {
        $params = [];
        foreach ($this->attributes as $attribute) {
            $value = $this->owner->{$attribute};
            $params[$attribute] = $value instanceof DateTime ? (string)$value : $value;
        }
        $this->owner->{$this->parameterName} = Json::encode($params);
    }

    /**
     * Распаковка JSON-массива в параметры модели
     */
    public function unpackAttributes()
    {
        if (!empty($this->owner->{$this->parameterName})) {
            $params = array_intersect_key(Json::decode($this->owner->{$this->parameterName}), array_flip($this->attributes));
            foreach ($params as $param => $value) {
                $this->owner->{$param} = $value;
            }
        }
    }
}
