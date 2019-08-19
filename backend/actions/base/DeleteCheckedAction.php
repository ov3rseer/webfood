<?php

namespace backend\actions\base;

use backend\actions\BackendModelAction;
use common\models\ActiveRecord;
use common\models\document\Document;
use common\models\reference\Reference;
use common\models\reference\User;
use Exception;
use Throwable;
use Yii;

/**
 * Действие для удаления нескольких моделей
 */
class DeleteCheckedAction extends BackendModelAction
{
    /**
     * @inheritdoc
     * @throws Exception
     * @throws Throwable
     */
    public function run()
    {
        $model = $this->modelClass;
        if ($ids = Yii::$app->request->post('ids')) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                /** @var ActiveRecord[] $models */
                $models = $model::findAll($ids);
                foreach ($models as $model) {
                    if ($model instanceof Reference || $model instanceof Document) {
                        if ($model instanceof User) {
                            $model->is_active = false;
                            $model->save();
                        } else {
                            $model->delete();
                        }
                    } else if ($model instanceof Document) {
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
