<?php

namespace frontend\actions\product;

use common\models\document\Document;
use common\models\reference\Product;
use common\models\reference\Reference;
use frontend\actions\FrontendModelAction;
use Yii;
use yii\base\UserException;

class UpdateAction extends FrontendModelAction
{
    /**
     * @inheritdoc
     * @throws UserException
     */
    public function run()
    {
        $id = Yii::$app->request->post('id');
        $name = Yii::$app->request->post('name');
        $is_active = Yii::$app->request->post('is_active');
        $price = Yii::$app->request->post('price');
        $unit_id = Yii::$app->request->post('unit_id');
        $category_id = Yii::$app->request->post('category_id');
        $is_active = $is_active == 'true';
        if ($id) {
            /** @var Product $model */
            $model = $this->controller->findModel($id, Product::class);
            if ($model instanceof Reference || $model instanceof Document) {
                $model->name = $name;
                $model->is_active = $is_active;
                $model->unit_id = $unit_id;
                $model->price = $price;
                $model->product_category_id = $category_id;
                $model->save();
            }
        }
        return true;
    }
}