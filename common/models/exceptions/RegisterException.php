<?php

namespace common\models\exceptions;

use common\models\document\Document;
use Exception;
use yii\base\UserException;

class RegisterException extends UserException
{
    /**
     * @var Document документ
     */
    public $document;

    /**
     * @var array ошибки проведения документа по регистрам
     */
    public $errors = [];

    /**
     * Конструктор
     * @param Document $document
     * @param array $errors
     * @param integer $code
     * @param Exception $previous
     */
    public function __construct($document = null, $errors = array(), $code = 0, Exception $previous = null)
    {
        $message = 'Документ не сохранен. <a href="javascript:void(showRegistersErrors())">Подробнее</a>';
        parent::__construct($message, $code, $previous);
        $this->document = $document;
        $this->errors = $errors;
    }
}
