<?php

namespace backend\controllers\reference;

use backend\controllers\BackendModelController;
use common\helpers\ArrayHelper;
use common\models\ActiveRecord;
use common\models\reference\MealCategory;
use common\models\reference\ProductCategory;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

abstract class CategoryController extends BackendModelController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['move'],
                        'allow' => true,
                        'roles' => ['super-admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UserException
     * @throws InvalidConfigException
     */
    public function actionMove()
    {
        /** @var ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $categoryId = Yii::$app->request->getBodyParam('id');
        $newParentId = Yii::$app->request->getBodyParam('newParentId');
        if (!$categoryId || !$newParentId) {
            throw new BadRequestHttpException();
        }
        /** @var MealCategory|ProductCategory $category */
        $category = $categoryId ? $this->findModel($categoryId, $this->modelClass) : null;
        if ($newParentId == '#') {
            $newParentId = null;
        } elseif (!$modelClass::find()->andWhere(['id' => $newParentId, 'parent_id' => null])->exists()) {
            return ['result' => false];
        }
        $category->parent_id = $newParentId;
        $category->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['result' => true];
    }
}