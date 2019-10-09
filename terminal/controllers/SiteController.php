<?php

namespace terminal\controllers;

use common\models\enum\FoodType;
use common\models\reference\Complex;
use common\models\reference\Meal;
use common\models\reference\MealCategory;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\Pagination;
use yii\helpers\Html;
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
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());

        $query = null;
        if (isset($requestData['categoryId'])) {
            $category = MealCategory::findOne(['id' => $requestData['categoryId']]);
            $query = Meal::find()->andWhere(['meal_category_id' => $category->id]);
        } else {
            $query = Complex::find();
        }
        $query = $query->andWhere([
            'is_active' => true,
            'food_type_id' => FoodType::BUFFET]
        );

        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSizeLimit' => [1, 9]
        ]);
        $models = $query->orderBy('id ASC')->offset($pages->offset)->limit($pages->limit)->all();

        $foods = [];
        foreach ($models as $model) {
            $foods[$model->id] = [
                'name' => Html::encode($model),
                'category' => isset($model->mealCategory) ? Html::encode($model->mealCategory) : 'Комплексы',
                'description' => Html::encode($model->description),
                'price' => $model->price,
                'parts' => [],
            ];

            $parts = $model->mealProducts ?? $model->complexMeals ?? [];
            $parameter = isset($model->mealProducts) ? 'product' : 'meal';

            foreach ($parts as $part) {
                $foods[$model->id]['parts'][Html::encode($part->{$parameter})] = [
                    'quantity' => (float)$part->{($parameter . '_quantity')},
                    'unit' => isset($part->unit->name) ? Html::encode($part->unit->name) : ''
                ];
            }
        }
        return $this->render('index', ['foods' => $foods, 'pages' => $pages]);
    }
}
