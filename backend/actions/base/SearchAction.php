<?php

namespace backend\actions\base;

use backend\actions\BackendModelAction;
use common\models\ActiveRecord;
use common\queries\ActiveQuery;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\web\Response;

/**
 * Действие для быстрого поиска моделей по ключевой фразе
 */
class SearchAction extends BackendModelAction
{
    /**
     * @var array поля модели, по которым осуществляется поиск
     */
    public $searchFields = [];

    /**
     * Построение запроса
     * @param string $term
     * @param string $conditions
     * @return ActiveQuery $this
     * @throws InvalidConfigException
     * @throws NotSupportedException
     */
    protected function buildQuery($term = '', $conditions = '')
    {
        $modelClass = $this->modelClass;
        if (!is_subclass_of($modelClass, ActiveRecord::class, true)) {
            throw new NotSupportedException('Модель не поддерживается');
        }
        $query = $modelClass::find()->andWhere($conditions)->limit(10);
        /** @var ActiveRecord $model */
        $model = new $modelClass();
        if ($term && $this->searchFields) {
            foreach ($this->searchFields as $field) {
                $columnSchema = $model->getTableSchema()->getColumn($field);
                if (!$columnSchema) {
                    continue;
                }
                $schema = Yii::$app->db->schema;
                switch ($columnSchema->type) {
                    case $schema::TYPE_CHAR:
                    case $schema::TYPE_STRING:
                        $query->orWhere(['LIKE', 'LOWER(' . $field . ')', mb_strtolower($term)]);
                        break;
                    case $schema::TYPE_INTEGER:
                        $query->orWhere([$field => (integer)$term]);
                        break;
                    case $schema::TYPE_DOUBLE:
                    case $schema::TYPE_FLOAT:
                        $query->orWhere([$field => (float)$term]);
                        break;
                    case $schema::TYPE_BOOLEAN:
                        $query->orWhere([$field => (boolean)$term]);
                        break;
                }
            }
        }
        return $query;
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     * @throws InvalidConfigException
     */
    public function run($term = '', $condition = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        /** @var ActiveRecord[] $models */
        $models = $this->buildQuery($term, $condition)->all();
        foreach ($models as $model) {
            $result[] = [
                'id' => $model->primaryKey,
                'text' => (string)$model,
            ];
        }
        return $result;
    }
}
