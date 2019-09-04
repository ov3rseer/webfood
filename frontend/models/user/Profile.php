<?php

namespace frontend\models\user;

use common\models\reference\Employee;
use common\models\reference\Father;
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
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!empty(Yii::$app->user->identity->name_full)) {
            $this->name = Yii::$app->user->identity->name;
        }
        if (!empty(Yii::$app->user->identity->email)) {
            $this->email = Yii::$app->user->identity->email;
        }
        $profile = Yii::$app->user->identity->getProfile();
        if ($profile && ($profile instanceof Father) || ($profile instanceof Employee)) {
            $this->surname = $profile->surname;
            $this->forename = $profile->forename;
            $this->patronymic = $profile->patronymic;
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

            ['patronymic', 'trim'],
            ['patronymic', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'validateEmail'],

            [['password', 'password_repeat'], 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
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
            'patronymic'        => 'Отчество',
            'email'             => 'Email',
            'password'          => 'Пароль',
            'password_repeat'   => 'Повторите пароль',
        ]);
    }

    /**
     * @inheritdoc
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
                if ($user->email) {
                    /** @var Father|Employee $profile */
                    $profile = $user->getProfile();
                    if ($profile && ($profile instanceof Father) || ($profile instanceof Employee)) {
                        $profile->forename = $this->forename;
                        $profile->surname = $this->surname;
                        $profile->patronymic = $this->patronymic;
                        $profile->save();
                    }
                    $user->name = $this->name;
                    if (!empty($this->password)) {
                        $user->setPassword($this->password);
                    }
                }
                $user->email = $this->email;
                $user->save();
                Yii::$app->session->setFlash('success', 'Вы успешно изменили свои данные.');
            }
        }
    }
}