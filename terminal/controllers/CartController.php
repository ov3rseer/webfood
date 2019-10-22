<?php

namespace terminal\controllers;

use terminal\models\Cart;
use Yii;
use yii\data\ArrayDataProvider;

/**
 * Контроллер для управления формы корзины
 */
class CartController extends TerminalModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'terminal\models\Cart';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $result = parent::actions();
        unset($result['index']);
        return $result;
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        /** @var Cart $model */
        $model = new $this->modelClass();
        $session = Yii::$app->session;
        $columns = [];
        $dataProvider = new ArrayDataProvider([]);
        if (isset($session['meals'])) {
            $model->meals = $session['meals'];
            $columns = $model->getColumns();
            $dataProvider = $model->getDataProvider();
        }
        return $this->render('@terminal/views/cart/index', ['model' => $model, 'columns' => $columns, 'dataProvider' => $dataProvider]);
    }

    /**
     * @return string
     */
    public function actionCartEmptying()
    {
        $session = Yii::$app->session;
        if (isset($session['meals'])) {
            unset($session['meals']);
        }
    }

//    public function actionCartRevision()
//    {
//        $id = Yii::$app->request->post('id');
//        $qty = Yii::$app->request->post('qty');
//        $price = Yii::$app->request->post('price');
//        $category = Yii::$app->request->post('category');
//        $session = Yii::$app->session;
//        if (isset($id) && isset($qty) && isset($price) && isset($category)) {
//            $categoryType = $category == 'Комплексы' ? 'complexes' : 'meals';
//
//            if (!isset($session[$categoryType])) {
//                $session->set($categoryType, []);
//            }
//            $food = $session[$categoryType];
//
//
//
//            if (!isset($session['sum'])) {
//                $sum = $qty * $price;
//                $session['sum'] = $sum;
//            } else {
//                $totalSum = $session['sum'];
//                $totalSum += $qty * $price;
//                $session['sum'] = $totalSum;
//            }
//            $session[$categoryType] = $food;
//        }
//    }
}