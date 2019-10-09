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
    public function actionDeleteAllMeals()
    {
        $session = Yii::$app->session;
        if (isset($session['meals'])) {
            unset($session['meals']);
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @return string
     */
    public function actionDeleteMeal()
    {
        $session = Yii::$app->session;
        $mealId = Yii::$app->request->post('mealId');
        if ($mealId && isset($session['meals'])) {
            $meals = $session['meals'];
            if (isset($meals[$mealId])) {
                unset($meals[$mealId]);
                $session['meals'] = $meals;
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionCartRevision()
    {
        $requestData = Yii::$app->request->post();
        $session = Yii::$app->session;
        if (isset($requestData['qty']) && isset($requestData['id'])) {
            $quantity = $requestData['qty'];
            $id = $requestData['id'];
            if (!isset($session['meals'])) {
                $session->set('meals', []);
            }
            $meals = $session['meals'];
            if ($quantity == 0) {
                unset($meals[$id]);
            } else {
                $meals[$id] = $quantity;
            }
            $session['meals'] = $meals;
        }
        var_dump($session['meals']);
    }
}