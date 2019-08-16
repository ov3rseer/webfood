<?php

namespace terminal\controllers;

use Yii;
use yii\web\Controller;

class TerminalModelController extends Controller
{
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
}