<?php


use common\models\reference\Child;
use common\models\reference\SchoolClass;

return [
    'school-class-1#child-1' => [
        'parent_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-1')->primaryKey,
        'child_id' => $this->getFixtureModel(Child::class, 'child-1')->primaryKey,
    ],
    'school-class-2#child-2' => [
        'parent_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-2')->primaryKey,
        'child_id' => $this->getFixtureModel(Child::class, 'child-2')->primaryKey,
    ],
    'school-class-3#child-3' => [
        'parent_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-3')->primaryKey,
        'child_id' => $this->getFixtureModel(Child::class, 'child-3')->primaryKey,
    ],
    'school-class-4#child-4' => [
        'parent_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-4')->primaryKey,
        'child_id' => $this->getFixtureModel(Child::class, 'child-4')->primaryKey,
    ],
    'school-class-5#child-5' => [
        'parent_id' => $this->getFixtureModel(SchoolClass::class, 'school-class-5')->primaryKey,
        'child_id' => $this->getFixtureModel(Child::class, 'child-5')->primaryKey,
    ],
];
