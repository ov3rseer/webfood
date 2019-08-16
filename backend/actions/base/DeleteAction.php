    <?php

    namespace backend\actions\base;

    use backend\actions\BackendModelAction;
    use common\models\document\Document;
    use common\models\enum\DocumentStatus;
    use common\models\reference\Reference;
    use common\models\reference\User;
    use Throwable;
    use yii\base\UserException;
    use yii\db\StaleObjectException;
    use yii\web\NotFoundHttpException;
    use yii\web\Response;

    /**
     * Действие для удаления существующей модели
     */
    class DeleteAction extends BackendModelAction
    {
        /**
         * @inheritdoc
         * @param $id
         * @return Response
         * @throws Throwable
         * @throws UserException
         * @throws StaleObjectException
         * @throws NotFoundHttpException
         */
        public function run($id)
        {
            $model = $this->controller->findModel($id, $this->modelClass);
            if ($model instanceof Reference || $model instanceof Document) {
                if ($model instanceof Reference) {
                    if ($model instanceof User) {
                        $model->is_active = false;
                    } else {
                        $model->delete();
                    }
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
