<?php

namespace frontend\models;

/**
 * Class System
 * @package backend\models\system
 *
 * @property string $name Название модели
 */
abstract class SystemForm extends FrontendForm
{
    /**
     * Выполнение действия
     * @return mixed
     */
    abstract public function proceed();

    /**
     * Получение наименования формы
     * @return string
     */
    abstract public function getName();
}