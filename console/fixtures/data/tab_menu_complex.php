<?php

use common\models\reference\Complex;
use common\models\reference\Menu;

return [
    'menu-1#complex-1' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-1')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-1')->primaryKey,
    ],
    'menu-1#complex-5' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-1')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-5')->primaryKey,
    ],
    'menu-2#complex-2' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-2')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-2')->primaryKey,
    ],
    'menu-2#complex-4' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-2')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-4')->primaryKey,
    ],
    'menu-3#complex-3' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-3')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-3')->primaryKey,
    ],
    'menu-3#complex-5' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-3')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-5')->primaryKey,
    ],
    'menu-4#complex-4' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-4')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-4')->primaryKey,
    ],
    'menu-4#complex-2' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-4')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-2')->primaryKey,
    ],
    'menu-5#complex-5' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-5')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-1')->primaryKey,
    ],
    'menu-5#complex-1' => [
        'parent_id' => $this->getFixtureModel(Menu::class, 'menu-5')->primaryKey,
        'complex_id' =>  $this->getFixtureModel(Complex::class, 'complex-1')->primaryKey,
    ],
];