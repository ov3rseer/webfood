<?php

namespace console\fixtures;

use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\test\ActiveFixture;

abstract class Fixture extends ActiveFixture
{
    /**
     * @var Fixture[]
     */
    protected static $_fixtures = [];

    /**
     * @inheritdoc
     * @throws NotSupportedException
     * @throws Exception
     */
    public function beforeLoad()
    {
        $this->checkIntegrity(false);
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     * @throws Exception
     */
    public function afterLoad()
    {
        $this->checkIntegrity(true);
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     * @throws Exception
     */
    public function beforeUnload()
    {
        $this->checkIntegrity(false);
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     * @throws Exception
     */
    public function afterUnload()
    {
        $this->checkIntegrity(true);
    }

    /**
     * Toggles the DB integrity check.
     * @param bool $check whether to turn on or off the integrity check.
     * @throws NotSupportedException
     * @throws Exception
     */
    public function checkIntegrity($check)
    {
        $this->db->createCommand()->checkIntegrity($check)->execute();
    }

    /**
     * @inheritdoc
     * @throws Exception
     * @throws NotSupportedException
     * @throws InvalidConfigException
     */
    public function load()
    {
        parent::load();
        self::$_fixtures[$this->modelClass] = $this;
        $this->db->createCommand()->resetSequence($this->getTableSchema()->fullName)->execute();
    }

    /**
     * Получение модели по имени класса и ключу
     * @param string $modelClass
     * @param string $name
     * @return ActiveRecord|null
     * @throws InvalidConfigException
     */
    public function getFixtureModel($modelClass, $name)
    {
        $fixture = isset(self::$_fixtures[$modelClass]) ? self::$_fixtures[$modelClass] : null;
        $result = $fixture ? $fixture->getModel($name) : null;
        if ($result) {
            $result->refresh();
        }
        return $result;
    }

    /**
     * Получение моделей фикстуры по имени класса
     * @param string $modelClass
     * @return array
     */
    public function getFixtureModels($modelClass)
    {
        $fixture = isset(self::$_fixtures[$modelClass]) ? self::$_fixtures[$modelClass] : null;
        $data = $fixture ? $fixture->data : [];
        return array_map(function ($key) use ($modelClass) {
            return $this->getFixtureModel($modelClass, $key);
        }, array_keys($data));
    }
}
