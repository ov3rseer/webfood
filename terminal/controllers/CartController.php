<?php

namespace terminal\controllers;

use common\models\document\Purchase;
use common\models\enum\DocumentStatus;
use common\models\reference\CardChild;
use common\models\reference\Meal;
use common\models\tablepart\PurchaseMeal;
use terminal\models\Cart;
use Yii;
use yii\base\UserException;
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
        if (isset($session['foods'])) {
            $foods = $session['foods'];
            $cartFoods = [];
            foreach ($foods as $category => $food) {
                $cartFoods[] = $food;
            }
            $model->foods = $cartFoods;
            $columns = $model->getColumns();
            $dataProvider = $model->getDataProvider();
        }
        return $this->render('@terminal/views/cart/index', ['model' => $model, 'columns' => $columns, 'dataProvider' => $dataProvider]);
    }


    /**
     * @throws UserException
     */
    public function actionPay()
    {
        $requestData = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
        $session = Yii::$app->session;
        if (isset($requestData['cardNumber']) && isset($session['sum'])) {
            $card = CardChild::findOne(['card_number' => $requestData['cardNumber']]);
            if ($card && $card->balance > $session['sum']) {
                $foods = $session['foods'];
                $purchase = new Purchase();
                $purchase->status_id = DocumentStatus::POSTED;
                $purchase->card_id = $card->id;
                $purchaseMeals = [];
                foreach ($foods as $id => $food) {
                    $meal = Meal::findOne(['id' => $id]);
                    if ($meal) {
                        $purchaseMeal = new PurchaseMeal();
                        $purchaseMeal->meal_id = $meal->id;
                        $purchaseMeal->quantity = $food['qty'];
                        $purchaseMeals[] = $purchaseMeal;
                    }
                }
                $purchase->populateRelation('purchaseMeals', $purchaseMeals);
                $purchase->save();
                if (isset($session['foods'])) {
                    unset($session['foods']);
                }
            } else {
                Yii::$app->session->setFlash('error', 'На карте недостаточно средств.');
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @return string
     */
    public function actionCartEmptying()
    {
        $session = Yii::$app->session;
        if (isset($session['foods'])) {
            unset($session['foods']);
        }
        if (isset($session['sum'])) {
            unset($session['sum']);
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionCartRevision()
    {
        $id = Yii::$app->request->post('id');
        $qty = Yii::$app->request->post('qty');
        $price = Yii::$app->request->post('price');
        $category = Yii::$app->request->post('category');
        if (isset($id) && !empty($qty) && isset($price) && isset($category)) {
            $session = Yii::$app->session;
            if (!isset($session['foods'])) {
                $session->set('foods', []);
            }
            if (!isset($session['sum'])) {
                $session->set('sum', null);
            }
            $foods = $session['foods'];
            $foods[$id]['qty'] = $qty;
            $foods[$id]['price'] = $price;
            $totalSum = 0;
            foreach ($foods as $id => $food) {
                $totalSum += $food['qty'] * $food['price'];
            }
            $session['foods'] = $foods;
            $session['sum'] = $totalSum;
        }
    }
}