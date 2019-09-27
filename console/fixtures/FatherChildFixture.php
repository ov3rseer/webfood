<?php

namespace console\fixtures;

class FatherChildFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\FatherChild';

    public $depends = [
        'console\fixtures\ChildFixture',
        'console\fixtures\FatherFixture',
    ];
}