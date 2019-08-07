<?php

namespace backend\actions\system\export;

use common\components\DateTime;
use common\models\document\Request;
use common\models\enum\DocumentStatus;
use common\models\tablepart\RequestDate;
use DOMDocument;
use Exception;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use ZipArchive;

/**
 * Действие для выгрузки предварительных заявок на следующую неделю
 */
class ExportManyRequestsAction extends Action
{
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function run()
    {
        $startNextWeek = new DateTime('next monday');
        $endNextWeek = clone $startNextWeek;
        $endNextWeek->modify('+ 7 days');

        /** @var Request[] $requests */
        $requests = Request::find()
            ->alias('r')
            ->innerJoin(RequestDate::tableName() . ' AS rd', 'r.id = rd.parent_id')
            ->andWhere(['between', 'rd.week_day_date', $startNextWeek, $endNextWeek])
            ->andWhere(['r.status_id' => [DocumentStatus::DRAFT, DocumentStatus::POSTED]])
            ->with('requestDates')
            ->all();
        if ($requests) {
            $zipPath = Yii::getAlias('@uploads') . '/requests/';
            if (!is_dir($zipPath)){
                mkdir($zipPath);
            }
            $zipName = 'Выгрузка предварительных заявок с ' . $startNextWeek->format('d.m.Y') . ' по ' . $endNextWeek->format('d.m.Y') . '.zip';
            $zip = new ZipArchive();
            if (file_exists($zipPath . $zipName)) {
                unlink($zipPath . $zipName);
            }
            if ($zip->open($zipPath . $zipName, ZipArchive::CREATE) !== true) {
                exit("Невозможно открыть <$zipName>\n");
            }
            $domAttributes = [];
            foreach ($requests as $request) {
                $domDocument = new DOMDocument('1.0', 'UTF-8');
                $domDocument->encoding = 'UTF-8';
                $domDocument->formatOutput = true;
                $domDocument->preserveWhiteSpace = false;

                $domElement = $domDocument->createElement('Контрагент');
                $domAttributes['КодКонтрагента'] = $request->service_object_code;
                $domAttributes['Контрагент'] = $request->serviceObject;
                $domAttributes['КодДоговора'] = $request->contract_code;
                $domAttributes['НаименованиеДоговора'] = $request->contract;
                $domAttributes['АдресДоставки'] = $request->address;
                foreach ($domAttributes as $attribute => $value) {
                    $domAttribute = $domDocument->createAttribute($attribute);
                    $domAttribute->value = $value;
                    $domElement->appendChild($domAttribute);
                }

                $quantities = [];
                $products = [];
                foreach ($request->requestDates as $requestDate) {
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

                $temp = tempnam(sys_get_temp_dir(), 'request');
                $domDocument->save($temp);
                $xmlName = 'Еженедельная заявка на поставку товара к объекту обслужиавания ' . $request->serviceObject . ' №' . $request->id . ' от ' . $request->date->format('d-m-Y') . '.xml';
                $zip->addFile($temp, $xmlName);
            }
            $zip->close();
            header('Content-Disposition: attachment;filename="' . $zipName . '"');
            header('Content-Type: application/zip');
            readfile($zipPath . $zipName);
        } else {
            Yii::$app->session->setFlash('info', 'Не найдены заявки на следующую неделю.');
            $this->controller->redirect('index');
        }
    }
}