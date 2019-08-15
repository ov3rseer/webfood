<?php

namespace frontend\controllers\serviceObject\request;

use common\helpers\ArrayHelper;
use frontend\controllers\FrontendModelController;
use yii\filters\AccessControl;

class RequestController extends FrontendModelController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['service-object'],
                    ],
                ],
            ],
        ]);
    }
}