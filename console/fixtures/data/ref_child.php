<?php

use common\models\reference\Father;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use console\fixtures\Fixture;

/** @var Fixture $this */
/** @noinspection PhpUnhandledExceptionInspection */

$keys = ['child-1', 'child-2', 'child-3', 'child-4', 'child-5'];

$children['child-1'] = [
    'surname' => 'Ерофеев',
    'forename' => 'Михаил',
    'patronymic' => 'Валентинович',
    'father_id' => $this->getFixtureModel(Father::class, 'father-1')->primaryKey,
    'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-1')->primaryKey,
];
$children['child-2'] = [
    'surname' => 'Симонов',
    'forename' => 'Константин',
    'patronymic' => 'Савельевич',
    'father_id' => $this->getFixtureModel(Father::class, 'father-1')->primaryKey,
    'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-2')->primaryKey,
];
$children['child-3'] = [
    'surname' => 'Королёва',
    'forename' => 'Елизавета',
    'patronymic' => 'Андреевна',
    'father_id' => $this->getFixtureModel(Father::class, 'father-1')->primaryKey,
    'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-3')->primaryKey,
];
$children['child-4'] = [
    'surname' => 'Исакова',
    'forename' => 'Екатерина',
    'patronymic' => 'Владимировна',
    'father_id' => $this->getFixtureModel(Father::class, 'father-2')->primaryKey,
    'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-4')->primaryKey,
];
$children['child-5'] = [
    'surname' => 'Наумов',
    'forename' => 'Эдуард',
    'patronymic' => 'Александрович',
    'father_id' => $this->getFixtureModel(Father::class, 'father-2')->primaryKey,
    'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-5')->primaryKey,
];

$result = [];
foreach ($keys as $key) {
    $result[$key] = [
        'is_active' => true,
        'name' => $children[$key]['surname'] . ' '
            . mb_substr($children[$key]['forename'], 0, 1) . '. '
            . mb_substr($children[$key]['patronymic'], 0, 1) . '.',
        'name_full' => $children[$key]['surname'] . ' '
            . $children[$key]['forename'] . ' '
            . $children[$key]['patronymic'],
        'surname' => $children[$key]['surname'],
        'forename' => $children[$key]['forename'],
        'patronymic' => $children[$key]['patronymic'],
        'service_object_id' => $this->getFixtureModel(ServiceObject::class, 'object-1')->primaryKey,
        'school_class_id' => $children[$key]['school_class_id'],
        'father_id' => $children[$key]['father_id'],
    ];
}
return $result;