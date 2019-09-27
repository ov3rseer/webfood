<?php

namespace console\controllers;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\db\Connection;
use yii\db\Exception;
use yii\di\Instance;

/**
 * Контроллер для пересоздания базы данных
 */
class DatabaseController extends Controller
{
    /**
     * @var Connection|string подключение к серверу базы данных
     */
    public $connection;

    /**
     * @var string название базы данных, которую надо пересоздать
     */
    public $name;

    /**
     * @var string пользователь, которому будет принадлежать база данных
     */
    public $owner;

    /**
     * @var string действие контроллера по умолчанию
     */
    public $defaultAction = 'recreate';

    /**
     * Действие "Пересоздание базы данных"
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionRecreate()
    {
        Yii::$app->db->close();
        $this->connection = Instance::ensure($this->connection, BaseObject::className());
        $this->connection->createCommand('DROP DATABASE IF EXISTS "' . $this->name . '"')->execute();
        $this->connection->createCommand('CREATE DATABASE"' . $this->name . '" WITH OWNER = "' . $this->owner . '" ENCODING "UTF-8" LC_COLLATE "ru_RU.UTF-8" LC_CTYPE "ru_RU.UTF-8" ')->execute();
        $this->connection->createCommand('GRANT ALL PRIVILEGES ON DATABASE "' . $this->name . '" TO "' . $this->owner . '"')->execute();
        Yii::$app->db->schema->refresh();
    }
}