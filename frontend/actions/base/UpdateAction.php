<?php

namespace frontend\actions\base;

use common\models\ActiveRecord;
use frontend\actions\FrontendModelAction;
use ReflectionException;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Действие для вывода формы редактирования существующей модели
 */
class UpdateAction extends FrontendModelAction
{
    /**
     * @var string путь к файлу представления для вкладок
     */
    public $tabsViewPath;

    /**
     * @inheritdoc
     * @param $id
     * @return array|string|Response
     * @throws ReflectionException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws UserException
     * @throws NotFoundHttpException
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
