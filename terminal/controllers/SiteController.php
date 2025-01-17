<?php

namespace terminal\controllers;

use common\components\DateTime;
use common\models\enum\FoodType;
use common\models\enum\MenuCycle;
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
        if ($setMenu && $setMenu->menu) {
            $foodIds = [];
            if (isset($setMenu->menu->menuMeals)) {
                foreach ($setMenu->menu->menuMeals as $menuMeal) {
                    $foodIds[] = $menuMeal->meal_id;
                }
                $query = Meal::find()->andWhere([
                    'id' => $foodIds,
                    'is_active' => true,
                    'food_type_id' => FoodType::BUFFET
                ]);
                if (isset($requestData['categoryId'])) {
                    $category = MealCategory::findOne(['id' => $requestData['categoryId']]);
                    if ($category) {
                        $query->andWhere(['meal_category_id' => $category->id]);
                    }
                }

//            else if (isset($setMenu->menu->menuComplexes)) {
//                foreach ($setMenu->menu->menuComplexes as $menuComplex) {
//                    $foodIds[] = $menuComplex->complex_id;
//                }
//                $query = Complex::find()->andWhere(['id' => $foodIds]);
//            }

                $countQuery = clone $query;
                $pages = new Pagination([
                    'totalCount' => $countQuery->count(),
                    'pageSizeLimit' => [1, 9]
                ]);
                $models = $query->orderBy('id ASC')->offset($pages->offset)->limit($pages->limit)->all();
            }

            $foods = [];
            if ($models) {
                foreach ($models as $model) {
                    $icon = 'fa-th-list';
                    if (isset($model->meal_category_id)) {
                        switch ($model->meal_category_id) {
                            case 3:
                            case 1:
                                $icon = 'fa-mortar-pestle';
                                break;
                            case 2:
                                $icon = 'fa-drumstick-bite';
                                break;
                            case 4:
                                $icon = 'fa-mug-hot';
                                break;
                            case 5:
                                $icon = 'fa-bread-slice';
                                break;
                            case 6:
                                $icon = 'fa-leaf';
                                break;
                        }
                    }
                    $foods[$model->id] = [
                        'name' => Html::encode($model),
                        'icon' => $icon,
                        'category' => $model->mealCategory,
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
            }
        }
        return $this->render('index', ['foods' => $foods, 'pages' => $pages]);
    }
}