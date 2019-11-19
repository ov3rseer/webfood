<?php


namespace console\fixtures;


class ServiceObjectSchoolClassFixture extends Fixture
{
    public $modelClass = 'common\models\tablepart\ServiceObjectSchoolClass';

    public $depends = [
        'console\fixtures\ServiceObjectFixture',
        'console\fixtures\SchoolClassFixture',
    ];
}