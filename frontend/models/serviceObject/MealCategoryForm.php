<?php

namespace frontend\models\serviceObject;

use common\models\reference\MealCategory;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\data\ActiveDataProvider;

/**
 * Форма добавления категорий блюд
 */
class MealCategoryForm extends CategoryForm
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Категории блюд';

    }

    /**
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function getDataProvider()
    {
        $query = MealCategory::find();

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 20,
                'pageSizeLimit' => false,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);
    }

    /**
     * @inheritdoc
     * @throws UserException
     */
    public function proceed()
    {
        $productCategory = MealCategory::findOne(['name' => $this->name]);
        if (!$productCategory) {
            $productCategory = new MealCategory();
            $productCategory->name = $this->name;
            $productCategory->is_active = true;
            $productCategory->save();
            Yii::$app->session->setFlash('success', 'Категория успешно добавлена.');
        } else {
            Yii::$app->session->setFlash('error', 'Такая категория же существует');
        }
    }
}