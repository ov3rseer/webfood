<?php

namespace backend\controllers\reference;

use common\helpers\ArrayHelper;
use common\models\reference\SystemSetting;

/**
 * Контроллер для справочника "Настройки системы"
 */
class SystemSettingController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\SystemSetting';

    /**
     * @inheritdoc
     */
    public function generateAutoColumns($model, $filterModel)
    {
        $result = array_merge(parent::generateAutoColumns($model, $filterModel), [
            'data' => [
                'attribute' => 'data',
                'format' => 'raw',
                'value' => function($rowModel) {
                    /** @var SystemSetting $rowModel */
                    return $rowModel->getFormattedValue();
                },
            ],
        ]);
        return ArrayHelper::filter($result, ['id', 'name', 'name_full', 'data']);
    }
}