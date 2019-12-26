<?php

namespace frontend\actions\base;

use common\models\ActiveRecord;
use common\models\document\Document;
use common\models\enum\DocumentStatus;
use common\models\reference\Reference;
use frontend\actions\FrontendModelAction;
use yii\base\UserException;
use yii\db\Exception;

class DeleteCheckedAction extends FrontendModelAction
{
    /**
     * @inheritdoc
     * @return bool
     * @throws \Throwable
     * @throws UserException
     * @throws Exception
     */
    public function run()
    {
        $model = $this->modelClass;
        if ($ids = \Yii::$app->request->post('ids')) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                /** @var ActiveRecord[] $models */
                $models = $model::findAll($ids);
                foreach ($models as $model) {
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
                }
                $transaction->commit();
            } catch (Exception $exception) {
                $transaction->rollBack();
                throw $exception;
            }
        }
        return true;
    }
}