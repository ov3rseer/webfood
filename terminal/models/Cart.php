<?php

namespace terminal\models;

use common\models\form\Report;
use Yii;
use yii\data\ArrayDataProvider;

class Cart extends Report
{
    public $foods;

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
        if (isset($session['foods'])) {
            return new ArrayDataProvider(['allModels' => $session['foods']]);
        }
        return new ArrayDataProvider([]);
    }
}