<?php

namespace backend\actions\base;

use backend\actions\BackendModelAction;
use common\models\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Действие для вывода формы просмотра существующей модели
 */
class ViewAction extends BackendModelAction
{
    /**
     * @var string путь к файлу представления для вкладок
     */
    public $tabsViewPath;

    /**
     * @inheritdoc
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        /** @var ActiveRecord $model */
        $model = $this->controller->findModel($id, $this->modelClass);
        return $this->controller->renderUniversal($this->viewPath, ['model' => $model]);
    }
}
