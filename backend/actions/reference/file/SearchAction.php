<?php

namespace backend\actions\reference\file;

use common\models\reference\File;
use yii\web\Response;

/**
 * Действие для быстрого поиска моделей по ключевой фразе
 */
class SearchAction extends \backend\actions\reference\base\SearchAction
{
    /**
     * @inheritdoc
     */
    public function run($term = '')
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        /** @var File[] $models */
        $models = $this->buildQuery($term)->all();
        foreach ($models as $model) {
            $result[] = [
                'id' => $model->primaryKey,
                'text' => (string)$model . ($model->name_full ? ' (' . $model->name_full . ')' : ''),
            ];
        }
        return $result;
    }
}
