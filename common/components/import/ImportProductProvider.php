<?php

namespace common\components\import;

use common\components\TaskProcessorInterface;
use common\models\reference\ConsoleTask;
use common\models\reference\File;
use common\models\reference\ProductProvider;
use SimpleXMLIterator;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\helpers\Json;

class ImportProductProvider extends BaseObject implements TaskProcessorInterface
{
    /**
     * @param ConsoleTask $consoleTask
     * @return array
     * @throws UserException
     * @throws InvalidConfigException
     */
    public function processTask($consoleTask)
    {
        $params = Json::decode($consoleTask->params);
        if (empty($params['file_id'])) {
            throw new UserException('Не указан файл для загрузки.');
        }
        /** @var File $file */
        $file = File::find()->where(['id' => $params['file_id']])->one();
        if (!$file) {
            throw new UserException('Указанный файл не обнаружен');
        }

        $result = [
            'added' => 0,
            'skipped' => 0,
        ];

        $xml = file_get_contents($file->getOriginalPath());
        $xml = new SimpleXMLIterator($xml);

        $objects = $xml->children();
        foreach ($objects as $object) {
            $name = trim($object['Наименование']);
            /** @var ProductProvider $productProvider */
            $productProvider = ProductProvider::find()->andWhere(['name' => $name])->one();
            if (!$productProvider) {
                $productProvider = new ProductProvider();
                $productProvider->name = $name;
                $productProvider->is_active = false;
                $productProvider->city = trim($object['Город']);
                $productProvider->zip_code = trim($object['Индекс']);
                $productProvider->address = trim($object['Адрес']);
                $productProvider->save();
                $result['added']++;
            } else {
                $result['skipped']++;
            }
        }

        return [
            'result_text' =>
                'Добавлено: ' . $result['added'] . '<br>' .
                'Пропущено: ' . $result['skipped'] . '<br><br>',
            'result_data' => $result,
        ];
    }
}