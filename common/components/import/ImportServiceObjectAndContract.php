<?php

namespace common\components\import;

use common\components\DateTime;
use common\components\TaskProcessorInterface;
use common\models\enum\ServiceObjectType;
use common\models\reference\ConsoleTask;
use common\models\reference\Contract;
use common\models\reference\File;
use common\models\reference\Product;
use common\models\reference\ServiceObject;
use common\models\reference\Unit;
use common\models\tablepart\ServiceObjectContract;
use common\models\tablepart\ContractProduct;
use DateInterval;
use Exception;
use SimpleXMLIterator;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\helpers\Json;

class ImportServiceObjectAndContract extends BaseObject implements TaskProcessorInterface
{
    /**
     * @param ConsoleTask $consoleTask
     * @return array
     * @throws UserException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function processTask($consoleTask)
    {
        $params = Json::decode($consoleTask->params);
        if (empty($params['files_id'])) {
            throw new UserException('Не указан(ы) файл(ы) для загрузки.');
        }
        if (empty($params['contract_type_id'])) {
            throw new UserException('Не указан тип договора.');
        }
        if (empty($params['service_object_type_id'])) {
            throw new UserException('Не указан тип объекта ослуживания.');
        }
        /** @var File $file */
        $files = File::find()->where(['id' => $params['files_id']])->all();
        if (!$files) {
            throw new UserException('Указанный(е) файл(ы) не обнаружен(ы)');
        }
        $result = [
            'added' => 0,
            'skipped' => 0,
        ];

        ////Парсер тут

        $serviceObjects = [];
        $contract_code = 0; // потом стереть

        foreach ($files as $file) {

            $xml = file_get_contents($file->getOriginalPath());
            $xml = new SimpleXMLIterator($xml);

            $service_object_code = trim($xml['КодКонтрагента']);

            if (!isset($serviceObjects[$service_object_code])) {
                $serviceObjects[$service_object_code] = [
                    'name' => trim($xml['Контрагент']),
                    'service_object_code' => $service_object_code,
                    'contracts' => [],
                ];
            }

            //$contract_code = trim($xml['НомерДоговора']); // потом раскоментить

            //if (!isset($serviceObjects[$service_object_code]['contracts'][$contract_code])) { // потом раскоментить
            $serviceObjects[$service_object_code]['contracts'][$contract_code] = [
                'name' => trim($xml['НаименованиеДоговора']),
                'contract_code' => trim($xml['КодДоговора']),
                'contract_type_id' => $params['contract_type_id'],
                'address' => trim($xml['АдресДоставки']),
                'date_from' => $xml['ДатаДоговора'],
                'products' => [],
            ];
            //} // потом раскоментить

            $childs = $xml->children();

            for ($i = 1; $i < count($childs); $i++) {
                $child = $childs[$i]->attributes();
                $unit_id = Unit::findOne(['name' => trim($child['ЕдиницаИзмерения'])])->id;
                $serviceObjects[$service_object_code]['contracts'][$contract_code]['products'][] = [
                    'name' => trim($child['Номенклатура']),
                    'product_code' => trim($child['Код']),
                    'unit_id' => $unit_id ? $unit_id : 2,
                ];
            }
            $contract_code++; // потом стереть

        }

        foreach ($serviceObjects as $service_object_code => $service_object_values) {

            $serviceObject = ServiceObject::findOne(['service_object_code' => $service_object_code]) ?: new ServiceObject();
            $serviceObject->name = $service_object_values['name'];
            $serviceObject->service_object_code = $service_object_values['service_object_code'];
            $serviceObject->service_object_type_id = ServiceObjectType::KINDERGARTEN;
            $serviceObject->save() ? $result['added']++ : $result['skipped']++;

            foreach ($service_object_values['contracts'] as $contract_code => $contract_values) {
                $contract = Contract::findOne(['contract_code' => $contract_values['contract_code']]) ?: new Contract();
                $contract->name = $contract_values['name'];
                $contract->contract_code = $contract_values['contract_code'];
                $contract->contract_type_id = $contract_values['contract_type_id'];
                $contract->date_from = (new DateTime($contract_values['date_from']))->format(DateTime::DB_DATETIME_FORMAT);
                $contract->date_to = (new DateTime($contract_values['date_from']))->add(new DateInterval('P1Y'))->format(DateTime::DB_DATETIME_FORMAT);
                $contract->save() ? $result['added']++ : $result['skipped']++;

                foreach ($contract_values['products'] as $product_values) {
                    $product = Product::findOne(['product_code' => $product_values['product_code']]) ?: new Product();
                    $product->name = $product_values['name'];
                    $product->product_code = $product_values['product_code'];
                    $product->unit_id = $product_values['unit_id'];

                    /////Править две следующие строки
                    $product->price = 0;
                    $product->product_category_id = 1;
                    $product->save() ? $result['added']++ : $result['skipped']++;

                    $contractProduct = ContractProduct::findOne(['parent_id' => $contract->id, 'product_id' => $product->id]) ?: new ContractProduct();
                    $contractProduct->parent_id = $contract->id;
                    $contractProduct->product_id = $product->id;
                    $contractProduct->save() ? $result['added']++ : $result['skipped']++;
                }

                $serviceObjectContract = ServiceObjectContract::findOne(['parent_id' => $serviceObject->id, 'contract_id' => $contract->id]) ?: new ServiceObjectContract();
                $serviceObjectContract->parent_id = $serviceObject->id;
                $serviceObjectContract->contract_id = $contract->id;
                $serviceObjectContract->address = $contract_values['address'];
                $serviceObjectContract->save() ? $result['added']++ : $result['skipped']++;
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