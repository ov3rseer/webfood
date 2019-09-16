<?php

namespace terminal\controllers;

use common\models\reference\Meal;
use terminal\models\MealForm;
use Yii;

/**
 * Контроллер для формы блюда
 */
class MealFormController extends TerminalModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'terminal\models\MealForm';

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
     * @inheritdoc
     */
    public function actionIndex()
    {
        /** @var MealForm $model */
        $model = new $this->modelClass();
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());

        $meal = null;
        if (isset($requestData['mealId'])) {
            $meal = Meal::findOne(['id' => $requestData['mealId']]);
        }
        if ($meal) {
            $model->meal_id = $meal->id;
            if ($model->load($requestData) && $model->validate()) {
                $model->proceed();
            }
            return $this->renderUniversal('@terminal/views/meal-form/index', ['model' => $model]);
        }

        Yii::$app->session->setFlash('info', 'Такого блюда не существует');
        $url = ['site/index'];
        if (isset($requestData['categoryId'])) {
            $url = array_merge($url, ['categoryId' => $requestData['categoryId']]);

        }
        return $this->redirect($url);
    }
}