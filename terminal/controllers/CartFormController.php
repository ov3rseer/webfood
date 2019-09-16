<?php

namespace terminal\controllers;

use terminal\models\CartForm;
use Yii;
use yii\data\ArrayDataProvider;

/**
 * Контроллер для управления формы корзины
 */
class CartFormController extends TerminalModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'terminal\models\CartForm';

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
        /** @var CartForm $model */
        $model = new $this->modelClass();
        $session = Yii::$app->session;
        $columns = [];
        $dataProvider = new ArrayDataProvider([]);
        if (isset($session['meals'])) {
            $model->meals = $session['meals'];
            $columns = $model->getColumns();
            $dataProvider = $model->getDataProvider();
        }
        return $this->render('@terminal/views/cart-form/index', ['model' => $model, 'columns' => $columns, 'dataProvider' => $dataProvider]);
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
}