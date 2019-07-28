<?php

use common\components\DbManager;
use yii\base\Application;
use yii\BaseYii;

/**
 * Класс приложения для работы подсказок IDE
 *
 * @property common\components\pgsql\Connection $db
 * @method common\components\pgsql\Connection getDb() getDb()
 * @property yii\mutex\PgsqlMutex $mutex
 * @property User $user
 * @method User getUser() getUser()
 * @property DbManager $authManager
 */
abstract class CodeAssistApplication extends Application
{
}

/**
 * Класс компонента пользователя
 *
 * @property common\models\reference\User $identity
 */
abstract class User extends yii\web\User
{
}

/**
 * Вспомогательный класс для работы подсказок IDE
 */
class Yii extends BaseYii
{
    /**
     * @var \yii\console\Application|\yii\web\Application|CodeAssistApplication the application instance
     */
    public static $app;
}
