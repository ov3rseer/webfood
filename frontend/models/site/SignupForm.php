<?php

namespace frontend\models\site;

use common\models\enum\UserType;
use common\models\reference\Father;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use common\models\reference\User;
use yii\base\UserException;

/**
 * Signup form
 */
class SignupForm extends Model
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
    public $patronymic;

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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'unique', 'targetClass' => '\common\models\reference\User', 'message' => 'Это имя пользователя уже занято.'],
            ['name', 'string', 'min' => 2, 'max' => 255],

            ['forename', 'trim'],
            ['forename', 'required'],
            ['forename', 'string', 'min' => 2, 'max' => 255],

            ['surname', 'trim'],
            ['surname', 'required'],
            ['surname', 'string', 'min' => 2, 'max' => 255],

            ['patronymic', 'trim'],
            ['patronymic', 'required'],
            ['patronymic', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\reference\User', 'message' => 'Этот адрес электронной почты уже занят.'],

            [['password', 'password_repeat'], 'trim'],
            [['password', 'password_repeat'], 'required'],
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
            'patronymic'        => 'Отчество',
            'email'             => 'Email',
            'password'          => 'Пароль',
            'password_repeat'   => 'Повторите пароль',
        ]);
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     * @throws Exception
     * @throws UserException
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        $user = new User();
        $user->email = $this->email;
        $user->name = $this->name;
        $user->user_type_id = UserType::FATHER;
        $user->is_active = false;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->save();

        $father = new Father();
        $father->user_id = $user->id;
        $father->surname = $this->surname;
        $father->forename = $this->forename;
        $father->patronymic = $this->patronymic;
        $father->save();

        return $this->sendEmail($user);
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
            ->setSubject('Зарегистрирована учетная запись в системе ' . Yii::$app->name)
            ->send();
    }
}
