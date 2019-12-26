<?php

namespace frontend\actions\base;

use common\models\document\Document;
use common\models\enum\DocumentStatus;
use common\models\reference\Reference;
use frontend\actions\FrontendModelAction;
use yii\base\UserException;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class DeleteAction extends FrontendModelAction
{
    /**
     * @inheritdoc
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws UserException
     * @throws \Throwable
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