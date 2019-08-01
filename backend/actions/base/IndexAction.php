<?php

namespace backend\actions\base;

use backend\actions\BackendModelAction;
use backend\widgets\ActiveForm;
use common\models\ActiveRecord;
use ReflectionException;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Response;

/**
 * Действие для вывода списка моделей
 */
class IndexAction extends BackendModelAction
{
    /**
     * @var ActiveRecord имя класса модели для фильтрации
     */
    public $filterModelClass;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!$this->filterModelClass) {
            $this->filterModelClass = $this->modelClass;
        }
    }

    /**
     * @inheritdoc
     * @return array|string
     * @throws ReflectionException
     * @throws InvalidConfigException
     */
    public function run()
    {
        $modelClass = $this->modelClass;
        $filterModelClass = $this->filterModelClass;
        /** @var ActiveRecord $model */
        $model = new $modelClass();
        /** @var ActiveRecord $filterModel */
        $filterModel = new $filterModelClass(['scenario' => $filterModelClass::SCENARIO_SEARCH]);
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            $filterModel->load(Yii::$app->request->get());
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($filterModel);
        }
        return $this->controller->renderUniversal($this->viewPath, [
            'model'        => $model,
            'filterModel'  => $filterModel,
            'dataProvider' => $filterModel->search(Yii::$app->request->get())
        ]);
    }
}
