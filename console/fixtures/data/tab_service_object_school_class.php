<?php

use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;

return [
    'object-1#school-class-1' => [
        'parent_id' => $this->getFixtureModel(ServiceObject::class, 'object-1')->primaryKey,
        'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-1')->primaryKey,
    ],
    'object-1#school-class-2' => [
        'parent_id' => $this->getFixtureModel(ServiceObject::class, 'object-1')->primaryKey,
        'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-2')->primaryKey,
    ],
    'object-1#school-class-3' => [
        'parent_id' => $this->getFixtureModel(ServiceObject::class, 'object-1')->primaryKey,
        'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-3')->primaryKey,
    ],
    'object-1#school-class-4' => [
        'parent_id' => $this->getFixtureModel(ServiceObject::class, 'object-1')->primaryKey,
        'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-4')->primaryKey,
    ],
    'object-1#school-class-5' => [
        'parent_id' => $this->getFixtureModel(ServiceObject::class, 'object-1')->primaryKey,
        'school_class_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-5')->primaryKey,
    ],
];