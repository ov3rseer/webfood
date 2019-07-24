<?php

namespace common\components\import;

use common\models\reference\Contract;
use common\models\reference\Contractor;
use common\models\reference\File;
use common\models\reference\Product;
use common\models\reference\Unit;
use common\models\tablepart\ContractorContract;
use DateInterval;
use DateTime;
use SimpleXMLIterator;
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
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function processTask($consoleTask)
    {
        $params = Json::decode($consoleTask->params);
        if (empty($params['files_id'])) {
            throw new UserException('Не указан(ы) файл(ы) для загрузки');
        }
        if (empty($params['columns'])) {
            throw new UserException('Не указаны колонки для загрузки');
        }
        /** @var File $file */
        $files = File::find()->where(['id' => $params['files_id']])->all();
        if (!$file) {
            throw new UserException('Указанный(е) файл(ы) не обнаружен(ы)');
        }
        $result = [
            'added' => 0,
            'skipped' => 0,
        ];

        ////Парсер тут

        $contractors = [];
        $contract_code = 0; // потом стереть

        foreach ($files as $file) {

            $xml = file_get_contents($file->getOriginalPath());
            $xml = new SimpleXMLIterator($xml);

            $contractor_code = trim($xml['КодКонтрагента']);

            if (!isset($contractors[$contractor_code])) {
                $contractors[$contractor_code] = [
                    'name' => trim($xml['Контрагент']),
                    'contractor_code' => $contractor_code,
                    'contracts' => [],
                ];
            }

            //$contract_code = trim($xml['НомерДоговора']); // потом раскоментить

            //if (!isset($contractors[$contractor_code]['contracts'][$contract_code])) { // потом раскоментить
            $contractors[$contractor_code]['contracts'][$contract_code] = [
                'name' => trim($xml['НаименованиеДоговора']),
                'contract_code' => $contractor_code,
                'contract_type_id' => \common\models\enum\ContractType::CHILD,
                'address' => trim($xml['АдресДоставки']),
                'date_from' => $xml['ДатаДоговора'],
                'products' => [],
            ];
            //} // потом раскоментить

            $childs = $xml->children();

            for ($i = 1; $i < count($childs); $i++) {
                $child = $childs[$i]->attributes();
                $unit_id = Unit::findOne(['name' => trim($child['ЕдиницаИзмерения'])])->id;
                $contractors[$contractor_code]['contracts'][$contract_code]['products'][] = [
                    'name' => trim($child['Номенклатура']),
                    'product_code' => trim($child['Код']),
                    'unit_id' => $unit_id ? $unit_id : 2,
                ];
            }
            $contract_code++; // потом стереть

        }

        foreach ($contractors as $contractor_code => $contractor_values) {

            $contractor = Contractor::findOne(['contractor_code' => $contractor_code]) ?: new Contractor;
            $contractor->name = $contractor_values['name'];
            $contractor->contractor_code = $contractor_values['contractor_code'];
            $contractor->save() ? $result['added']++ : $result['skipped']++;

            foreach ($contractor_values['contracts'] as $contract_code => $contract_values) {
                $contract = Contract::findOne(['contract_code' => $contract_values['contract_code']]) ?: new Contract();
                $contract->name = $contract_values['name'];
                $contract->contract_code = $contract_values['contract_code'];
                $contract->contract_type_id = $contract_values['contract_type_id'];
                $contract->date_from = (new DateTime($contract_values['date_from']))->format('Y-m-d H:i:s');
                $contract->date_to = (new DateTime($contract_values['date_from']))->add(new DateInterval('P1Y'))->format('Y-m-d H:i:s');
                $contract->save() ? $result['added']++ : $result['skipped']++;

                foreach ($contract_values['products'] as $product_values) {
                    $product = Product::findOne(['product_code' => $product_values['product_code']]) ?: new Product();
                    $product->name = $product_values['name'];
                    $product->product_code = $product_values['product_code'];
                    $product->unit_id = $product_values['unit_id'];
                    $product->save() ? $result['added']++ : $result['skipped']++;
                }

                $contractorContract = ContractorContract::findOne(['parent_id' => $contractor->id, 'contract_id' => $contract->id]) ?: new ContractorContract();
                $contractorContract->parent_id = $contractor->id;
                $contractorContract->contract_id = $contract->id;
                $contractorContract->address = $contract_values['address'];
                $contractorContract->save() ? $result['added']++ : $result['skipped']++;
            }

        }

        return [
            'result_text' =>
                'Добавлено: ' . $result['added'] . '<br>' .
                'Пропущено: ' . $result['skipped'] . '<br><br>' .
                Html::a('Загруженный(е) файл(ы)', ['/reference/file/download', 'id' => $file->id], ['target' => '_blank']),
            'result_data' => $result,
        ];
    }
}