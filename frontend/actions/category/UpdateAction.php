<?php

namespace frontend\actions\category;

use common\models\document\Document;
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
        $category_name = Yii::$app->request->post('category_name');
        $is_active = Yii::$app->request->post('is_active');
        $is_active = $is_active == 'true';
        if ($id && $category_name) {
            $model = $this->controller->findModel($id, $this->controller->categoryModel);
            if ($model instanceof Reference || $model instanceof Document) {
                $model->name = $category_name;
                $model->is_active = $is_active;
                $model->save();
            }
        }
        return true;
    }
}