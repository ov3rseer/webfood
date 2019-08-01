<?php

namespace backend\actions\document\base;

use backend\controllers\document\DocumentController;
use backend\widgets\ActiveForm;
use common\components\DateTime;
use common\exceptions\RegisterException;
use common\models\document\Document;
use common\models\enum\DocumentStatus;
use common\models\system\Entity;
use Yii;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Действие для вывода формы создания новой модели
 */
class CreateAction extends \backend\actions\base\CreateAction
{
    /**
     * @var DocumentController
     */
    public $controller;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function run()
    {
        $registerErrors = [];
        /** @var Document $model */
        $model = $this->controller->createModel($this->modelClass);
        $model->date = new DateTime();
        $model->status_id = DocumentStatus::DRAFT;
        $model->load(Yii::$app->request->get(), '');
        if (Yii::$app->request->isAjax) {
            $model->load(Yii::$app->request->post());
            if (!Yii::$app->request->isPjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                if ($relationName = Yii::$app->request->post('addTablePartRow', false)) {
                    $model->validate();
                    $model->addNewTablePartRow($relationName);
                } else if ($relationName = Yii::$app->request->post('processTablePart', false)) {
                    $rule = Yii::$app->request->post('rule', false);
                    if ($rule) {
                        $model->processTablePartByRule($relationName, $rule);
                    }
                }
                return $this->controller->renderUniversal($this->viewPath, ['model' => $model, 'registerErrors' => $registerErrors]);
            }
        } else if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $oldStatusId = $model->getOldAttribute('status_id');
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Документ "' . $model . '" успешно создан');
                return $this->controller->autoRedirect(['update', 'id' => $model->id]);
            } catch (RegisterException $ex) {
                $registerErrors = $this->controller->prepareRegistersErrors($ex->errors);
                $transaction->rollBack();
                $model->status_id = $oldStatusId;
            } catch (\Exception $e) {
                $transaction->rollback();
                $model->status_id = $oldStatusId;
            }
        } else if (Yii::$app->request->isGet) {
            $basisId = Yii::$app->request->get('basis_id');
            $basisTypeId = Yii::$app->request->get('basis_type_id');
            if ($basisId && $basisTypeId) {
                /** @var Document $class */
                $class = Entity::getClassNameById($basisTypeId);
                if (!$class || !is_subclass_of($class, Document::class)) {
                    throw new BadRequestHttpException();
                }
                /** @var Document $basis */
                $basis = $class::findOne($basisId);
                if (!$basis) {
                    throw new BadRequestHttpException();
                }
                $model = $basis->createRelated($model::className());
            }
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model, 'registerErrors' => $registerErrors]);
    }
}
