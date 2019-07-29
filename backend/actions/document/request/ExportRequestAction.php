<?php

namespace backend\actions\document\request;

use backend\actions\BackendModelAction;
use common\models\document\Request;
use DOMDocument;
use yii\web\NotFoundHttpException;

class ExportRequestAction extends BackendModelAction
{
    /**
     * @inheritdoc
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        /** @var Request $model */
        $model = $this->controller->findModel($id, $this->modelClass);
        $domAttributes = [];
        $domDocument = new DOMDocument('1.0', 'UTF-8');
        $domDocument->encoding = 'UTF-8';
        $domDocument->formatOutput = true;
        $domDocument->preserveWhiteSpace = false;

        $domElement = $domDocument->createElement('Контрагент');
        $domAttributes['КодКонтрагента'] = $model->contractor_code;
        $domAttributes['Контрагент'] = $model->contractor;
        $domAttributes['КодДоговора'] = $model->contract_code;
        $domAttributes['НаименованиеДоговора'] = $model->contract;
        $domAttributes['АдресДоставки'] = $model->address;
        foreach ($domAttributes as $attribute => $value) {
            $domAttribute = $domDocument->createAttribute($attribute);
            $domAttribute->value = $value;
            $domElement->appendChild($domAttribute);
        }

        $quantities = [];
        $products = [];
        foreach ($model->requestDates as $requestDate) {
            foreach ($requestDate->requestDateProducts as $requestDateProduct) {
                if (isset($requestDateProduct->product)) {
                    $quantities[$requestDateProduct->product_id][] = $requestDateProduct->current_quantity;
                    $products[$requestDateProduct->product_id]['ЕдиницаИзмерения'] = $requestDateProduct->unit;
                    $products[$requestDateProduct->product_id]['Количество'] = array_sum($quantities[$requestDateProduct->product_id]);
                    $products[$requestDateProduct->product_id]['Номенклатура'] = $requestDateProduct->product;
                }
            }
        }
        foreach ($products as $productId => $product) {
            $domChildElement = $domDocument->createElement('ТабличнаяЧасть');
            foreach ($product as $attribute => $value) {
                $domAttribute = $domDocument->createAttribute($attribute);
                $domAttribute->value = $value;
                $domChildElement->appendChild($domAttribute);
            }
            $domElement->appendChild($domChildElement);
        }
        $domDocument->appendChild($domElement);

        $xmlName = 'Еженедельная заявка на поставку товара к контрагенту ' . $model->contractor . ' №' . $model->id . ' от ' . $model->date->format('d-m-Y');
        header('Content-Disposition: attachment;filename="' . $xmlName . 'xml"');
        header('Content-Type: application/xml');
        $domDocument->save("php://output");
    }
}