<?php

namespace backend\controllers;

use common\models\enum\UserType;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'export-object-authorization-data' => [
                'class' => 'backend\actions\system\export\ExportObjectAuthorizationDataAction',
            ],
            'export-provider-authorization-data' => [
                'class' => 'backend\actions\system\export\ExportProviderAuthorizationDataAction',
            ],
            'export-many-requests' => [
                'class' => 'backend\actions\system\export\ExportManyRequestsAction',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['export-object-authorization-data', 'export-provider-authorization-data', 'export-many-requests'],
                        'allow' => true,
                        'roles' => ['super-admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->user_type_id != UserType::ADMIN) {
            Yii::$app->user->logout();
            return $this->actionLogin();
        }
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
