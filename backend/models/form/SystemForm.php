<?php

namespace backend\models\form;

/**
 * Class SystemForm
 * @package backend\models
 *
 * @property string $name Название модели
 */
abstract class SystemForm extends Form
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