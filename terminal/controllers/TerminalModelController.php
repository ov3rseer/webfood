<?php

namespace terminal\controllers;

use common\models\ActiveRecord;
use common\models\form\Form;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TerminalModelController extends Controller
{
    /**
     * @var string имя класса модели
     */
    public $modelClass;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass не указан.');
        }
        if (!is_subclass_of($this->modelClass, ActiveRecord::class, true) &&
            !is_subclass_of($this->modelClass, Form::class, true)) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass не является подклассом ' .
                ActiveRecord::class . '.');
        }
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'terminal\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@terminal/views/base/index',
            ],
        ]);
    }

    /**
     * Создание новой модели
     * @param string|null $modelClass
     * @return ActiveRecord
     */
    public function createModel($modelClass = null)
    {
        /* @var ActiveRecord $modelClass */
        $modelClass = $modelClass ? $modelClass : $this->modelClass;
        return new $modelClass();
    }

    /**
     * Поиск модели по ее ID
     * @param string|integer $id
     * @param string|null $modelClass
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findModel($id, $modelClass = null)
    {
        /* @var ActiveRecord $modelClass */
        $modelClass = $modelClass ? $modelClass : $this->modelClass;
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $modelClass::findOne(array_combine($keys, $values));
            }
        } elseif ($id !== null) {
            $model = $modelClass::findOne($id);
        }
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('Элемент с указанным ID не найден: ' . $id);
        }
    }

    /**
     * Вывод представления в разных шаблонах в зависимости от типа запроса
     * @param string $view
     * @param array $params
     * @return string
     */
    public function renderUniversal($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($view, $params);
        } else {
            $layout = Yii::$app->request->get('layout', false);
            if ($layout) {
                $this->layout = $layout;
            }
            return $this->render($view, $params);
        }
    }

    /**
     * Перенаправление с учетом настроек, заданных в $_GET
     * @param array|string $url
     * @param int $statusCode
     * @return Response
     */
    public function autoRedirect($url, $statusCode = 302)
    {
        $newUrl = Yii::$app->request->get('redirect', false);
        if ($newUrl) {
            $url = $newUrl;
        } else if (is_array($url) && ($layout = Yii::$app->request->get('layout', false))) {
            $url['layout'] = $layout;
        }
        return $this->redirect($url, $statusCode);
    }
}