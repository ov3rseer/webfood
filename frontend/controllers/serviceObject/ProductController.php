<?php

namespace frontend\controllers\serviceObject;

use common\helpers\ArrayHelper;
use common\models\ActiveRecord;
use common\models\document\Document;
use common\models\reference\Product;
use common\models\reference\Reference;
use frontend\controllers\FrontendModelController;
use Yii;
use yii\base\UserException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Контроллер для формы "Продукты"
 */
class ProductController extends FrontendModelController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'frontend\models\serviceObject\ProductForm';

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
                        'actions' => ['index', 'delete-checked', 'delete', 'update'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'update' => ['POST'],
                    'delete' => ['POST'],
                    'delete-checked' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'frontend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@frontend/views/service-object/product/index',
            ],
        ]);
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    public function actionDeleteChecked()
    {
        if ($ids = Yii::$app->request->post('ids')) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                /** @var ActiveRecord[] $models */
                $models = Product::find()->andWhere(['id' => $ids])->all();
                foreach ($models as $model) {
                    if ($model instanceof Reference || $model instanceof Document) {
                        $model->is_active = false;
                        $model->save();
                    }
                }
                $transaction->commit();
            } catch (Exception $exception) {
                $transaction->rollBack();
                throw $exception;
            }
        }
        return true;
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     * @throws UserException
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id, Product::class);
        if ($model instanceof Reference || $model instanceof Document) {
            $model->is_active = false;
            $model->save();
        }
        return true;
    }

    /**
     * @return mixed
     * @throws NotFoundHttpException
     * @throws UserException
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');
        $name = Yii::$app->request->post('name');
        $is_active = Yii::$app->request->post('is_active');
        $product_code = Yii::$app->request->post('product_code');
        $price = Yii::$app->request->post('price');
        $unit_id = Yii::$app->request->post('unit_id');
        $category_id = Yii::$app->request->post('category_id');
        $is_active = $is_active == 'true';
        if ($id) {
            /** @var Product $model */
            $model = $this->findModel($id, Product::class);
            if ($model instanceof Reference || $model instanceof Document) {
                $model->name = $name;
                $model->is_active = $is_active;
                $model->product_code = $product_code;
                $model->unit_id = $unit_id;
                $model->price = $price;
                $model->product_category_id = $category_id;
                $model->save();
            }
        }
        return true;
    }
}