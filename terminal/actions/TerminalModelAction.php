<?php

namespace terminal\actions;

use terminal\controllers\TerminalModelController;
use terminal\models\form\Form;
use common\models\ActiveRecord;
use yii\base\Action;
use yii\base\InvalidConfigException;

/**
 * Базовый класс для действий с моделями
 */
class TerminalModelAction extends Action
{
    /**
     * @var ActiveRecord имя класса модели
     */
    public $modelClass;

    /**
     * @var TerminalModelController
     */
    public $controller;

    /**
     * @var string путь к файлу представления
     */
    public $viewPath;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass не указан.');
        }
        if (!is_subclass_of($this->modelClass, ActiveRecord::class, true) &&
            !is_subclass_of($this->modelClass, Form::class, true)) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass не является подклассом ' .
                ActiveRecord::class . '.');
        }
        $this->controller->modelClass = $this->modelClass;
    }
}