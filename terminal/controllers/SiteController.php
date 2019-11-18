<?php

namespace terminal\controllers;

use common\components\DateTime;
use common\models\enum\FoodType;
use common\models\enum\MenuCycle;
use common\models\reference\Complex;
use common\models\reference\Meal;
use common\models\reference\MealCategory;
use common\models\reference\SetMenu;
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

        $currentDate = new DateTime('now');
        $weekDayId = $currentDate->format('N');
        $menuCycleIds[] = MenuCycle::WEEKLY;
        if ($currentDate->format('W') % 2 == 0) {
            $menuCycleIds[] = MenuCycle::EVEN_WEEKS;
        } else {
            $menuCycleIds[] = MenuCycle::ODD_WEEKS;
        }
        /** @var SetMenu $setMenu */
        $setMenu = SetMenu::find()
            ->andWhere([
                'week_day_id' => $weekDayId,
                'is_active' => true,
                'menu_cycle_id' => $menuCycleIds
            ])
            ->one();

        $query = null;
        $pages = null;
        $models = null;
        if ($setMenu && $setMenu->menu && isset($requestData['categoryId'])) {
            $foodIds = [];
            if (isset($setMenu->menu->menuMeals)) {
                foreach ($setMenu->menu->menuMeals as $menuMeal) {
                    $foodIds[] = $menuMeal->meal_id;
                }
                $category = MealCategory::findOne(['id' => $requestData['categoryId']]);
                $query = Meal::find()->andWhere(['id' => $foodIds, 'meal_category_id' => $category->id]);

            } else if (isset($setMenu->menu->menuComplexes)) {
                foreach ($setMenu->menu->menuComplexes as $menuComplex) {
                    $foodIds[] = $menuComplex->complex_id;
                }
                $query = Complex::find()->andWhere(['id' => $foodIds]);
            }
            $query = $query->andWhere([
                'is_active' => true,
                'food_type_id' => FoodType::BUFFET
            ]);

            $countQuery = clone $query;
            $pages = new Pagination([
                'totalCount' => $countQuery->count(),
                'pageSizeLimit' => [1, 9]
            ]);
            $models = $query->orderBy('id ASC')->offset($pages->offset)->limit($pages->limit)->all();
        }

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
