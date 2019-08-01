<?php

namespace backend\actions\base;

use backend\actions\BackendModelAction;
use common\models\document\Document;
use common\models\enum\DocumentStatus;
use common\models\reference\Reference;
use yii\base\UserException;
use yii\web\NotFoundHttpException;

/**
 * Действие для восстановление существующей модели
 */
class RestoreAction extends BackendModelAction
{
    /**
     * @inheritdoc
     * @throws NotFoundHttpException
     * @throws UserException
     */
    public function run($id)
    {
        $model = $this->controller->findModel($id, $this->modelClass);
        if ($model instanceof Reference) {
            $model->is_active = true;
        } else if ($model instanceof Document) {
            $model->status_id = DocumentStatus::DRAFT;
        }
        $model->save();
        return $this->controller->autoRedirect(['update', 'id' => $model->id]);
    }
}
