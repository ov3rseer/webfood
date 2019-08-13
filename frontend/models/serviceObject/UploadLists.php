<?php

namespace frontend\models\serviceObject;

use frontend\models\FrontendForm;

class UploadLists extends FrontendForm
{
    /**
     * @var string
     */
    public $surname;

    /**
     * @var string
     */
    public $forename;

    /**
     * @var string
     */
    public $patronymic;

    /**
     * @var string
     */
    public $class_number;

    /**
     * @var string
     */
    public $class_litter;

    /**
     * @var string
     */
    public $code_word;

    /**
     * @var string
     */
    public $snils;

    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Загрузка списка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Загрузка списков';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surname', 'forename', 'patronymic', 'class_number', 'snils'], 'trim'],
            [['surname', 'forename', 'patronymic'], 'required'],
            [['surname', 'forename', 'patronymic'], 'string', 'min' => 2, 'max' => 255],
            [['class_litter'], 'string', 'max' => 1],
            [['class_number'], 'integer'],
            [['class_number'], 'in', range(1, 11), 'message' => 'Значение не должно быть больше 11.'],
        ];
    }

}