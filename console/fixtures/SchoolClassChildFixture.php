<?php

namespace console\fixtures;

class SchoolClassChildFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\SchoolClassChild';

    public $depends = [
        'console\fixtures\SchoolClassFixture',
        'console\fixtures\ChildFixture',
    ];
}