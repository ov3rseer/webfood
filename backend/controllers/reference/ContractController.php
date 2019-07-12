<?php

namespace backend\controllers\reference;

/**
 * Контроллер для справочника "Договоры"
 */
class ContractController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\Contract';
}