<?php

namespace frontend\controllers;

use common\models\enum\UserType;
use common\models\reference\Child;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use frontend\models\site\PasswordResetRequestForm;
use frontend\models\site\ResendVerificationEmailForm;
use frontend\models\site\ResetPasswordForm;
use frontend\models\site\SignupForm;
use frontend\models\site\VerifyEmailForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\bootstrap\Html;
use yii\web\BadRequestHttpException;
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
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        switch (Yii::$app->user->identity->user_type_id) {
            case UserType::ADMIN:
                return $this->render('@frontend/views/admin/index');
            case UserType::SERVICE_OBJECT:
                return $this->render('@frontend/views/service-object/index');
            case UserType::EMPLOYEE:
                return $this->render('@frontend/views/employee/index');
            case UserType::FATHER:
                return $this->render('@frontend/views/father/index');
            case UserType::PRODUCT_PROVIDER:
                return $this->render('@frontend/views/product-provider/index');
            default:
                return $this->render('index');
        }
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function actionSearchChild()
    {
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        $userInput = $requestData['userInput'];
        $list = [];
        if ($userInput) {
            $search = explode(' ', $userInput);
            $sql = ['OR'];
            foreach ($search as $word) {
                $sql[] = ['ilike', 'c.surname', $word];
                $sql[] = ['ilike', 'c.forename', $word];
                $sql[] = ['ilike', 'c.patronymic', $word];
            }

            $childrenQuery = Child::find()
                ->alias('c')
                ->select(['concat(c.name, \', \',sc.name, \', \', so.name) as name'])
                ->innerJoin(ServiceObject::tableName() . ' AS so', 'so.id = c.service_object_id')
                ->innerJoin(SchoolClass::tableName() . ' AS sc', 'sc.id = c.school_class_id')
                ->andWhere(['c.is_active' => true])
                ->filterWhere($sql)
                ->indexBy('id')
                ->asArray()
                ->column();

            foreach ($childrenQuery as $childId => $childName) {
                $list[] = ['value' => $childName, 'id' => $childId];
            }
        }
        $result = Html::beginTag('div', ['class' => 'list-group']);
        if (!empty($list)) {
            foreach ($list as $key => $item) {
                $result .= Html::a($item['value'], '#', ['class' => 'list-group-item', 'data-id' => $item['id']]);
            }
        } else {
            $result .= Html::a('Ничего не найдено!', '#', ['class' => 'list-group-item']);
        }
        $result .= Html::endTag('div');
        return $result;
    }

    /**
     * Logs in a user.
     *
     * @return mixed
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
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

//    /**
//     * Displays contact page.
//     *
//     * @return mixed
//     */
//    public function actionContact()
//    {
//        $model = new ContactForm();
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
//                Yii::$app->session->setFlash('success', 'Благодарим вас за обращение. Мы ответим вам как можно скорее.');
//            } else {
//                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
//            }
//
//            return $this->refresh();
//        } else {
//            return $this->render('contact', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Signs user up.
     *
     * @return mixed
     * @throws Exception
     * @throws UserException
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Спасибо за регистрацию. Пожалуйста, проверьте свой email для подтверждения регистрации.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     * @throws Exception
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для дальнейших инструкций.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'К сожалению, мы не можем сбросить пароль для указанного адреса электронной почты.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль сохранен.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return yii\web\Response
     * @throws UserException
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Ваш email был подтвержден!');
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', 'К сожалению, мы не можем подтвердить ваш аккаунт с помощью предоставленного токена.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Проверьте свою электронную почту для дальнейших инструкций.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'К сожалению, мы не можем прислать письмо для подтверждения на указанный адрес электронной почты.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
