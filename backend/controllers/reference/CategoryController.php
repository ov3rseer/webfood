<?php

namespace backend\controllers\reference;

use backend\controllers\BackendModelController;
use common\helpers\ArrayHelper;
use common\models\reference\MealCategory;
use Yii;
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
     * @throws UserException
     * @throws NotFoundHttpException
     */
    public function actionMove()
    {
        $categoryId = Yii::$app->request->getBodyParam('id');
        $newParentId = Yii::$app->request->getBodyParam('newParentId');
        if (!$categoryId || !$newParentId) {
            throw new BadRequestHttpException();
        }
        /** @var MealCategory $category */
        $category = $categoryId ? $this->findModel($categoryId, $this->modelClass) : null;
        if ($newParentId == '#') {
            $newParentId = null;
        }
        $category->parent_id = $newParentId;
        $category->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['result' => true];
    }
}