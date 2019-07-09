<?php

namespace backend\actions\base;

use backend\actions\ModelAction;
use common\models\document\Document;
use common\models\enum\DocumentStatus;
use common\models\reference\Reference;

/**
 * Действие для удаления существующей модели
 */
class DeleteAction extends ModelAction
{
    /**
     * @inheritdoc
     * @param $id
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \yii\base\UserException
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        $model = $this->controller->findModel($id, $this->modelClass);
        if ($model instanceof Reference || $model instanceof Document) {
            if ($model instanceof Reference) {
                $model->is_active = false;
            } else if ($model instanceof Document) {
                $model->status_id = DocumentStatus::DELETED;
            }
            $model->save();
        } else {
            $model->delete();
        }
        return $this->controller->autoRedirect(['index']);
    }
}
