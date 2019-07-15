<?php
namespace common\models;

use common\models\reference\User;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $login;
    public $password;
    public $rememberMe = true;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['login', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'login'         => 'Логин',
            'password'      => 'Пароль',
            'rememberMe'    => 'Запомнить меня'
        ]);
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     * @param $attribute
     * @throws \yii\base\InvalidConfigException
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверный логин или пароль.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     * @throws \yii\base\InvalidConfigException
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return array|User|null
     * @throws \yii\base\InvalidConfigException
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByNameOrEmail($this->login);
        }
        return $this->_user;
    }
}
