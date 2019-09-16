<?php

namespace terminal\models;

use common\models\form\SystemForm;
use common\models\reference\Meal;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Базовая модель элемента справочника
 *
 * @property integer $meal_id
 * @property integer $quantity
 *
 * Отношения:
 * @property Meal $meal
 */
class MealForm extends SystemForm
{
    public $meal_id;
    public $quantity;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['meal_id'], 'integer'],
            [['quantity'], 'integer', 'min' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'quantity' => 'Количество',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Html::encode($this->meal);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getMeal()
    {
        return Meal::find()->andWhere(['id' => $this->meal_id]);
    }

    /**
     * @inheritdoc
     */
    public function proceed()
    {
        $session = Yii::$app->session;
        if (!isset($session['meals'])) {
            $session->set('meals', []);
        }
        if (!isset($session['meals'][$this->meal_id])) {
            $meals = $session['meals'];
            $meals[$this->meal_id] = (integer)$this->quantity;
            $session['meals'] = $meals;
        } else {
            $meals = $session['meals'];
            foreach ($meals as $mealId => $quantity) {
                if ($mealId == $this->meal_id) {
                    $meals[$mealId] += $this->quantity;
                }
            }
            $session['meals'] = $meals;
        }
        return Yii::$app->controller->redirect(Yii::$app->request->referrer);
    }
}