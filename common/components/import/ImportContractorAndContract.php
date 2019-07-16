<?php

namespace common\components\import;

use common\models\reference\File;
use yii\base\BaseObject;
use yii\base\UserException;
use yii\helpers\Html;
use yii\helpers\Json;

class ImportContractorAndContract extends BaseObject implements TaskProcessorInterface
{
    /**
     * @param \common\models\reference\ConsoleTask $consoleTask
     * @return array
     * @throws UserException
     */
    public function processTask($consoleTask)
    {
        $params = Json::decode($consoleTask->params);
        if (empty($params['file_id'])) {
            throw new UserException('Не указан файл для загрузки');
        }
        if (empty($params['columns'])) {
            throw new UserException('Не указаны колонки для загрузки');
        }
        /** @var File $file */
        $file = File::findOne($params['file_id']);
        if (!$file) {
            throw new UserException('Указанный файл не обнаружен');
        }
        $result = [
            'added' => 0,
            'skipped' => 0,
        ];

        ////Парсер тут


        return [
            'result_text' =>
                'Добавлено: ' . $result['added'] . '<br>' .
                'Пропущено: ' . $result['skipped'] . '<br><br>' .
                Html::a('Загруженный файл', ['/reference/file/download', 'id' => $file->id], ['target' => '_blank']),
            'result_data' => $result,
        ];
    }
}