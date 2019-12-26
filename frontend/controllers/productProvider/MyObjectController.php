<?php

namespace frontend\controllers\productProvider;

use common\helpers\ArrayHelper;
use common\models\reference\ProductProvider;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class MyObjectController extends Controller
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
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['product-provider'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $result = parent::actions();
        unset($result['index']);
        return $result;
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $productProvider = null;
        if (Yii::$app->user) {
            /** @var ProductProvider $productProvider */
            $productProvider = ProductProvider::findOne(['user_id' => Yii::$app->user->id]);
            if ($productProvider === null) {
                throw new NotFoundHttpException('Вы не являетесь поставщиком, чтобы просматривать данную страницу.');
            }
        }
        return $this->render('index', ['productProvider' => $productProvider]);
    }
}