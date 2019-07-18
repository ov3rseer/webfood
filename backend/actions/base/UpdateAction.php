<?php

namespace backend\actions\base;

use backend\actions\BackendModelAction;
use common\models\ActiveRecord;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Действие для вывода формы редактирования существующей модели
 */
class UpdateAction extends BackendModelAction
{
    /**
     * @var string путь к файлу представления для вкладок
     */
    public $tabsViewPath;

    /**
     * @inheritdoc
     * @param $id
     * @return array|string|Response
     * @throws \ReflectionException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UserException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /** @var ActiveRecord $model */
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
                return $this->controller->renderUniversal($this->viewPath, ['model' => $model, 'activeTabRelation' => $relationName]);
            }
        } else if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            Yii::$app->session->setFlash('success', 'Элемент "' . $model . '" успешно сохранен');
            return $this->controller->autoRedirect(['', 'id' => $model->id]);
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
