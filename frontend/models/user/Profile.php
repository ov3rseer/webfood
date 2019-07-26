<?php

namespace frontend\models\user;

use common\models\reference\User;
use frontend\models\FrontendForm;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;

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
        if (!empty(Yii::$app->user->identity->name_full)) {
            $this->name = Yii::$app->user->identity->name;
        }
        if (!empty(Yii::$app->user->identity->surname)) {
            $this->surname = Yii::$app->user->identity->surname;
        }
        if (!empty(Yii::$app->user->identity->forename)) {
            $this->forename = Yii::$app->user->identity->forename;
        }
        if (!empty(Yii::$app->user->identity->email)) {
            $this->email = Yii::$app->user->identity->email;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'string', 'min' => 2, 'max' => 255],
            ['name', 'validateName'],

            ['forename', 'trim'],
            ['forename', 'string', 'min' => 2, 'max' => 255],

            ['surname', 'trim'],
            ['surname', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'validateEmail'],

            [['password', 'password_repeat'], 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
        ];
    }

    /**
     * Проверка на уникальность name
     * @throws InvalidConfigException
     */
    public function validateName()
    {
        $user = User::findByName($this->name);
        if ($this->name != Yii::$app->user->identity->name && $user) {
            $this->addError('email', 'Этот адрес электронной почты уже занят.');
        }
    }

    /**
     * Проверка на уникальность email
     * @throws InvalidConfigException
     */
    public function validateEmail()
    {
        $user = User::findByEmail($this->email);
        if ($this->email != Yii::$app->user->identity->email && $user) {
            $this->addError('email', 'Этот адрес электронной почты уже занят.');
        }
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
     * @throws Exception
     */
    public function proceed()
    {
        $userId = Yii::$app->user->id;
        if ($this->email && $userId) {
            /** @var User $user */
            $user = User::findIdentity($userId);
            if ($user) {
                $user->email = $this->email;
                $user->name = $this->name;
                $user->name_full = Yii::$app->user->identity->name_full ?? $this->surname . ' ' . $this->forename;
                $user->forename = ucfirst($this->forename);
                $user->surname = ucfirst($this->surname);
                $user->setPassword($this->password);
                $user->generateAuthKey();
                $user->save();
            }
        }
    }
}