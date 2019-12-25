<?php

namespace frontend\controllers\serviceObject;

use common\helpers\ArrayHelper;
use common\models\ActiveRecord;
use common\models\document\Document;
use common\models\reference\Reference;
use frontend\controllers\FrontendModelController;
use Yii;
use yii\base\UserException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Базовый контроллер для форм "Категорий"
 */
abstract class CategoryController extends FrontendModelController
{
    /**
     * @var string имя модели категорий
     */
    public $categoryModel = null;

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
                'modelClassForm' => $this->modelClassForm,
                'viewPath' => '@frontend/views/service-object/category/index',
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
        if ($this->categoryModel) {
            if ($ids = Yii::$app->request->post('ids')) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    /** @var ActiveRecord[] $models */
                    $models = $this->categoryModel::find()->andWhere(['id' => $ids])->all();
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
        $model = $this->findModel($id, $this->categoryModel);
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
        $category_name = Yii::$app->request->post('category_name');
        $is_active = Yii::$app->request->post('is_active');
        $is_active = $is_active == 'true';
        if ($id && $category_name) {
            $model = $this->findModel($id, $this->categoryModel);
            if ($model instanceof Reference || $model instanceof Document) {
                $model->name = $category_name;
                $model->is_active = $is_active;
                $model->save();
            }
        }
        return true;
    }
}