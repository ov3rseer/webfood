<?php

namespace backend\actions\base;

use backend\actions\ModelAction;
use common\models\document\Document;
use common\models\enum\DocumentStatus;
use common\models\reference\Reference;

/**
 * Действие для восстановление существующей модели
 */
class RestoreAction extends ModelAction
{
    /**
     * @inheritdoc
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\base\UserException
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
