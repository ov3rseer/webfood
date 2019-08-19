<?php

namespace terminal\models;

/**
 * Class System
 * @package terminal\models\system
 *
 * @property string $name Название модели
 */
abstract class SystemForm extends TerminalForm
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