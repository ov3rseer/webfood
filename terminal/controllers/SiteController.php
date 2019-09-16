<?php

namespace terminal\controllers;

use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (!empty($requestData['categoryId'])) {
            return $this->render('index', ['categoryId' => $requestData['categoryId']]);
        }
        return $this->render('index');
    }
}
