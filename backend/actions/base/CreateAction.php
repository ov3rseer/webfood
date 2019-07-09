<?php

namespace backend\actions\base;

use backend\actions\ModelAction;
use common\models\ActiveRecord;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Действие для вывода формы создания новой модели
 */
class CreateAction extends ModelAction
{
    /**
     * @var string путь к файлу представления для вкладок
     */
    public $tabsViewPath;

    /**
     * @inheritdoc
     * @return array|string|Response
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\UserException
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
