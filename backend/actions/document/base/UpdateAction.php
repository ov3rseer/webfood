<?php

namespace backend\actions\document\base;

use backend\controllers\document\DocumentController;
use backend\widgets\ActiveForm;
use common\models\document\Document;
use common\models\exceptions\RegisterException;
use Yii;
use yii\web\Response;

/**
 * Действие для вывода формы редактирования существующей модели
 */
class UpdateAction extends \backend\actions\base\UpdateAction
{
    /**
     * @var DocumentController
     */
    public $controller;

    /**
     * @inheritdoc
     */
    public function run($id)
    {
        $registerErrors = [];
        /** @var Document $model */
        $model = $this->controller->findModel($id, $this->modelClass);
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
                } else {
                    $relationName = Yii::$app->request->post('deleteTablePartRow', false);
                }
                return $this->controller->renderUniversal($this->viewPath,
                    ['model' => $model, 'activeTabRelation' => $relationName, 'registerErrors' => $registerErrors]);
            }
        } else if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $oldStatusId = $model->getOldAttribute('status_id');
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Документ "' . $model . '" успешно сохранен');
                return $this->controller->autoRedirect(['', 'id' => $model->id]);
            } catch (RegisterException $ex) {
                $registerErrors = $this->controller->prepareRegistersErrors($ex->errors);
                $transaction->rollBack();
                $model->status_id = $oldStatusId;
            } catch (\Exception $e) {
                $transaction->rollback();
                $model->status_id = $oldStatusId;
            }
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model, 'registerErrors' => $registerErrors]);
    }
}
