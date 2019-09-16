<?php

namespace terminal\models;

use common\models\form\Report;
use Yii;
use yii\data\ArrayDataProvider;

class CartForm extends Report
{
    public $meals;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Корзина';
    }

    /**
     * @return array
     */
    public function getColumns()
    {

        return [];
    }

    /**
     * @return ArrayDataProvider
     */
    public function getDataProvider()
    {
        $session = Yii::$app->session;
        if (isset($session['meals'])) {
            return new ArrayDataProvider(['allModels' => $session['meals']]);
        }
        return new ArrayDataProvider([]);
    }
}