<?php

namespace frontend\models\serviceObject;

use common\models\reference\ProductCategory;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\data\ActiveDataProvider;

/**
 * Форма добавления категорий продуктов
 */
class ProductCategoryForm extends CategoryForm
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Категории продуктов';

    }

    /**
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function getDataProvider()
    {
        $query = ProductCategory::find();

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
        $productCategory = ProductCategory::findOne(['name' => $this->name]);
        if (!$productCategory) {
            $productCategory = new ProductCategory();
            $productCategory->name = $this->name;
            $productCategory->is_active = true;
            $productCategory->save();
        } else {
            Yii::$app->session->setFlash('error', 'Такая категория же существует');
        }
    }
}