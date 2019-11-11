<?php

use common\models\reference\Child;
use common\models\reference\Father;

return [
    'father-1#child-1' => [
        'parent_id' => $this->getFixtureModel(Father::class, 'father-1')->primaryKey,
        'child_id' =>  $this->getFixtureModel(Child::class, 'child-1')->primaryKey,
    ],
    'father-1#child-2' => [
        'parent_id' => $this->getFixtureModel(Father::class, 'father-1')->primaryKey,
        'child_id' => $this->getFixtureModel(Child::class, 'child-2')->primaryKey,
    ],
    'father-1#child-3' => [
        'parent_id' => $this->getFixtureModel(Father::class, 'father-1')->primaryKey,
        'child_id' => $this->getFixtureModel(Child::class, 'child-3')->primaryKey,
    ],
    'father-2#child-4' => [
        'parent_id' => $this->getFixtureModel(Father::class, 'father-2')->primaryKey,
        'child_id' => $this->getFixtureModel(Child::class, 'child-4')->primaryKey,
    ],
    'father-2#child-5' => [
        'parent_id' => $this->getFixtureModel(Father::class, 'father-2')->primaryKey,
        'child_id' => $this->getFixtureModel(Child::class, 'child-5')->primaryKey,
    ],
];