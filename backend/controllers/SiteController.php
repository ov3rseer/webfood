<?php
namespace backend\controllers;

use common\models\document\Request;
use common\models\enum\UserType;
use common\models\reference\User;
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
            'export-contractors-authorization-data' => [
                'class' => 'backend\actions\system\export\ExportContractorsAuthorizationDataAction',
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
                        'actions' => ['export-contractors-authorization-data'],
                        'allow' => true,
                        'roles' => [User::class . '.Index'],
                    ],
                    [
                        'actions' => ['export-many-requests'],
                        'allow' => true,
                        'roles' => [Request::class . '.Update'],
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
        if(Yii::$app->user->identity->user_type_id != UserType::ADMIN){
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
