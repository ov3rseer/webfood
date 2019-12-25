<?php

namespace frontend\actions;

use common\models\ActiveRecord;
use common\models\form\Form;
use frontend\controllers\FrontendModelController;
use yii\base\Action;
use yii\base\InvalidConfigException;

/**
 * Базовый класс для действий с моделями
 */
abstract class FrontendModelAction extends Action
{
    /**
     * @var ActiveRecord имя класса модели
     */
    public $modelClass;

    /**
     * @var ActiveRecord имя класса модели
     */
    public $modelClassForm;

    /**
     * @var FrontendModelController
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
        if ($this->modelClassForm === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClassForm не указан.');
        }
        if ($this->modelClass && !is_subclass_of($this->modelClass, ActiveRecord::class, true)) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass не существует или не является подклассом ' .
                ActiveRecord::class . '.');
        }
        if (!is_subclass_of($this->modelClassForm, Form::class, true)) {
            throw new InvalidConfigException(get_class($this) . '::$modelClassForm не является подклассом ' .
                Form::class . '.');
        }

        $this->controller->modelClass = $this->modelClass;
        $this->controller->modelClassForm = $this->modelClassForm;
    }
}
