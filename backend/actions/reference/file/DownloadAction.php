<?php

namespace backend\actions\reference\file;

use backend\actions\BackendModelAction;
use common\models\reference\File;
use yii\web\ForbiddenHttpException;

/**
 * Действие для скачивания файла по ID
 */
class DownloadAction extends BackendModelAction
{
    /**
     * @inheritdoc
     * @param null $id
     * @return \yii\console\Response|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id = null)
    {
        /** @var File $file */
        $file = $this->controller->findModel($id);
        if (!(\Yii::$app->user->can($this->controller::className() . '.Index') || $file->create_user_id == \Yii::$app->user->id)) {
            throw new ForbiddenHttpException();
        }
        return \Yii::$app->response->sendFile($file->getOriginalPath(), $file->name);
    }
}
