<?php


namespace console\fixtures;


class SetMenuFixture extends Fixture
{
    public $modelClass = 'common\models\reference\SetMenu';

    public $depends = [
        'console\fixtures\MenuFixture',
    ];
}