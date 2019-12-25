<?php

namespace frontend\actions\base;

use common\models\ActiveRecord;
use frontend\actions\FrontendModelAction;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class CreateAction extends FrontendModelAction
{
    /**
     * @var string путь к файлу представления для вкладок
     */
    public $tabsViewPath;

    /**
     * @inheritdoc
     * @param $id
     * @return array|string|Response
     * @throws InvalidConfigException
     * @throws UserException
     * @throws \ReflectionException
     */
    public function run()
    {
        /** @var ActiveRecord $model */
        $model = $this->controller->createModel($this->modelClass);
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
                }
                return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
            }
        } else if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            Yii::$app->session->setFlash('success', 'Элемент "' . $model . '" успешно создан');
            return $this->controller->autoRedirect(['update', 'id' => $model->id]);
        }
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}