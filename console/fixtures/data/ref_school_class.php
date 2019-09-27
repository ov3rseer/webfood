<?php

use common\models\reference\ServiceObject;
use console\fixtures\Fixture;

/** @var Fixture $this */
/** @noinspection PhpUnhandledExceptionInspection */

$serviceObject = ServiceObject::findOne(['id' => $this->getFixtureModel(ServiceObject::className(), 'object-1')->primaryKey]);

return [
    'school-class-1' => [
        'is_active' => true,
        'name' => '10А',
        'name_full' => '10А ' . (string)$serviceObject,
        'service_object_id' => $serviceObject->id,
        'number' => '10',
        'litter' => 'А'
    ],
    'school-class-2' => [
        'is_active' => true,
        'name' => '10Б',
        'name_full' => '10Б ' . (string)$serviceObject,
        'service_object_id' => $serviceObject->id,
        'number' => '10',
        'litter' => 'Б'
    ],
    'school-class-3' => [
        'is_active' => true,
        'name' => '10В',
        'name_full' => '10В ' . (string)$serviceObject,
        'service_object_id' => $serviceObject->id,
        'number' => '10',
        'litter' => 'В'
    ],
    'school-class-4' => [
        'is_active' => true,
        'name' => '8А',
        'name_full' => '8А ' . (string)$serviceObject,
        'service_object_id' => $serviceObject->id,
        'number' => '8',
        'litter' => 'А'
    ],
    'school-class-5' => [
        'is_active' => true,
        'name' => '8Б',
        'name_full' => '8Б ' . (string)$serviceObject,
        'service_object_id' => $serviceObject->id,
        'number' => '8',
        'litter' => 'Б'
    ],
];