<?php

namespace frontend\models\user;

use common\models\reference\User;
use frontend\models\FrontendForm;
use Yii;

/**
 * Форма "Профиль"
 */
class Profile extends FrontendForm
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $surname;

    /**
     * @var string
     */
    public $forename;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $password_repeat;

    public function init()
    {
        parent::init();
        $this->name = Yii::$app->user->identity->name_full;
        $this->surname = Yii::$app->user->identity->surname;
        $this->forename = Yii::$app->user->identity->forename;
        $this->email = Yii::$app->user->identity->email;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'unique', 'targetClass' => '\common\models\reference\User', 'message' => 'This username has already been taken.'],
            ['name', 'string', 'min' => 2, 'max' => 255],

            ['forename', 'trim'],
            ['forename', 'string', 'min' => 2, 'max' => 255],

            ['surname', 'trim'],
            ['surname', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\reference\User', 'message' => 'This email address has already been taken.',
                'when' => function($model){
                    return $model->email != Yii::$app->user->identity->email;
                }
            ],

            [['password', 'password_repeat'], 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name'              => 'Логин',
            'surname'           => 'Фамилия',
            'forename'          => 'Имя',
            'email'             => 'Email',
            'password'          => 'Пароль',
            'password_repeat'   => 'Повторите пароль',
        ]);
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     * @throws \yii\base\Exception
     */
    public function proceed()
    {
        $user = new User();
        $user->email = $this->email;
        $user->name = $this->name;
        $user->name_full = $this->surname . ' ' . $this->forename;
        $user->forename = $this->forename;
        $user->surname = $this->surname;
        $user->is_active = false;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        return $user->save() && $this->sendEmail($user);

    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}