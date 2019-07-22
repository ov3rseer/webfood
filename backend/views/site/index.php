<?php

/* @var $this yii\web\View */

use common\models\reference\File;
use common\models\reference\Unit;
use common\models\reference\User;
use common\models\reference\Contractor;
use common\models\reference\Contract;
use common\models\reference\Product;
use common\models\tablepart\ContractorContract;
use yii\base\UserException;
use yii\helpers\Html;
use yii\helpers\Json;

$this->title = 'Admin WebFood';
?>
<div class="site-index">

    <div class="jumbotron">

        <p><?= \yii\helpers\Html::a('Импорт контрагентов и договоров',
                ['/system/import-contractor-and-contract/index'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>
        <p><?= \yii\helpers\Html::a('Экспорт авторизационных данных новых контрагентов',
                ['export-contractors-authorization-data'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>
                ['/system/import-contractor-and-contract/index'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:520px;']) ?></p>

    <?php
        /** @var File $file */
        $files = File::find()->orderBy('id')->all();
        if (!$files) {
            throw new UserException('Указанный файл не обнаружен');
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
                    'name_full' => trim($xml['Контрагент']),
                    'address' => trim($xml['АдресДоставки']),
                    'contractor_code' => $contractor_code,
                    'contracts' => [],
                ];
            }

            //$contract_code = trim($xml['НомерДоговора']); // потом раскоментить

            //if (!isset($contractors[$contractor_code]['contracts'][$contract_code])) { // потом раскоментить
                $contractors[$contractor_code]['contracts'][$contract_code] = [
                    'name' => trim($xml['НаименованиеДоговора']),
                    'name_full' => trim($xml['НаименованиеДоговора']),
                    'contract_code' => $contractor_code,
                    'contract_type_id' => \common\models\enum\ContractType::CHILD,
                    'products' => [],
                ];
            //} // потом раскоментить

            $childs = $xml->children();

            for ($i = 1; $i < count($childs); $i++) {
                $child = $childs[$i]->attributes();
                $unit_id = Unit::findOne(['name' => trim($child['ЕдиницаИзмерения'])])->id;
                $contractors[$contractor_code]['contracts'][$contract_code]['products'][] = [
                    'name' => trim($child['Номенклатура']),
                    'name_full' => trim($child['Номенклатура']),
                    'code' => trim($child['Код']),
                    'unit_id' => $unit_id ? $unit_id : 2,
                ];
            }
            $contract_code++; // потом стереть

        }

        foreach ($contractors as $contractor_code => $contractor_values) {

            $contractor = Contractor::findOne(['contractor_code' => $contractor_code]) ?: new Contractor;
            $contractor->name = $contractor_values['name'];
            $contractor->name_full = $contractor_values['name_full'];
            $contractor->address = $contractor_values['address'];
            $contractor->contractor_code = $contractor_values['contractor_code'];
            $contractor->save() ? $result['added']++ : $result['skipped']++;

            foreach ($contractor_values['contracts'] as $contract_code => $contract_values) {
                $contract = Contract::findOne(['contract_code' => $contract_values['contract_code']]) ?: new Contract();
                $contract->name = $contract_values['name'];
                $contract->name_full = $contract_values['name_full'];
                $contract->contract_code = $contract_values['contract_code'];
                $contract->contract_type_id = $contract_values['contract_type_id'];
                $contract->save() ? $result['added']++ : $result['skipped']++;

                foreach ($contract_values['products'] as $product_values) {
                    $product = Product::findOne(['code' => $product_values['code']]) ?: new Product();
                    $product->name = $product_values['name'];
                    $product->name_full = $product_values['name_full'];
                    $product->code = $product_values['code'];
                    $product->unit_id = $product_values['unit_id'];
                    $product->save() ? $result['added']++ : $result['skipped']++;
                }

                $contractorContract = ContractorContract::findOne(['parent_id' => $contractor->id, 'contract_id' => $contract->id]) ?: new ContractorContract();
                $contractorContract->parent_id = $contractor->id;
                $contractorContract->contract_id = $contract->id;
                $contractorContract->save() ? $result['added']++ : $result['skipped']++;
            }

        }

        echo 'Добавлено: ' . $result['added'] . '<br>' .
            'Пропущено: ' . $result['skipped'] . '<br><br>' .
            Html::a('Загруженный файл', ['/reference/file/download', 'id' => $file->id], ['target' => '_blank']);

        /*return [
            'result_text' =>
                    'Добавлено: ' . $result['added'] . '<br>' .
                    'Пропущено: ' . $result['skipped'] . '<br><br>' .
                    Html::a('Загруженный файл', ['/reference/file/download', 'id' => $file->id], ['target' => '_blank']),
            'result_data' => $result,
        ];*/
    ?>

    </div>

</div>
