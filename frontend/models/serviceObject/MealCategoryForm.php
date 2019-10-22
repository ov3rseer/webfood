<?php

namespace frontend\models\serviceObject;

use common\models\reference\MealCategory;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\data\ActiveDataProvider;

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
        $productCategory = new MealCategory();
        $productCategory->name = $this->name;
        $productCategory->is_active = true;
        $productCategory->save();
    }
}