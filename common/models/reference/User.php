<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * Модель "Пользователь"
 *
 * @property integer $id
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $password
 * @property string $name
 * @property string $surname
 * @property string $username
 * @property string $password_reset_token
 * @property string $verification_token
 */
class User extends Reference implements IdentityInterface
{
    /**
     * @var string
     */
    protected $_password;

    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Пользователь';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Пользователи';
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->isNewRecord ? '(новый)' : ($this->surname ? $this->surname . ' ' . $this->name : $this->name);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['username'], 'string'],
            [['email'], 'filter', 'filter' => 'trim'],
            [['email', 'surname'], 'string', 'max' => 255],
            [['email', 'username', 'surname'], 'required'],
            [['email'], 'unique', 'message' => 'Этот email-адрес уже зарегистрирован.'],
            [['password'], 'string'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'username'  => 'Логин',
            'name'      => 'Имя',
            'surname'   => 'Фамилия',
            'email'     => 'Email',
            'password'  => 'Пароль',
        ]);
    }

    /**
     * @inheritdoc
     * @throws yii\base\InvalidConfigException
     */
    public static function findIdentity($id)
    {
        return static::find()->active()->andWhere(['id' => $id, 'is_active' => true])->one();
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return array|User|yii\db\ActiveRecord
     * @throws yii\base\InvalidConfigException
     */
    public static function findByEmail($email)
    {
        return static::find()->active()->andWhere(['LOWER(email)' => mb_strtolower($email)])->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return array|User|null
     * @throws yii\base\InvalidConfigException
     */
    public static function findByUsername($username)
    {
        return static::find()->active()->andWhere(['username' => $username])->one();
    }

    /**
     * Finds user by username or email
     *
     * @param string $username
     * @return array|User|null
     * @throws yii\base\InvalidConfigException
     */
    public static function findByUsernameOrEmail($login)
    {
        return static::find()->active()->andWhere(['OR', ['username' => $login], ['email' => $login]])->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->_password = $password;
        $this->password_hash = Yii::$app->security->generatePasswordHash($this->_password);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne(['password_reset_token' => $token, 'is_active' => true]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne(['verification_token' => $token, 'is_active' => false]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Generates "remember me" authentication key
     * @throws yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     * @throws Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * @throws Exception
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        $parentResult = parent::load($data, $formName);
        if ($parentResult) {
            $scope = $formName === null ? $this->formName() : $formName;
            if (isset($data[$scope]['password'])) {
                $this->password = $data[$scope]['password'];
            }
        }
        return $parentResult;
    }

    /**
     * @inheritdoc
     * @throws yii\base\Exception
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult && $this->isNewRecord && !$this->auth_key) {
            $this->auth_key = Yii::$app->security->generateRandomString();
        }
        return $parentResult;
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['is_active']['displayType'] = ActiveField::BOOL;
            $this->_fieldsOptions['password_hash']['displayType'] = ActiveField::IGNORE;
            $this->_fieldsOptions['auth_key']['displayType'] = ActiveField::IGNORE;
            $this->_fieldsOptions['verification_token']['displayType'] = ActiveField::IGNORE;
            $this->_fieldsOptions['password_reset_token']['displayType'] = ActiveField::IGNORE;
            $this->_fieldsOptions['email']['displayType'] = ActiveField::EMAIL;
        }
        return $this->_fieldsOptions;
    }

    /**
     * Проверка, что компонент пользователя загружен в backend-режиме
     * @return boolean
     * @throws \Throwable
     */
    public static function isBackendUser()
    {
        return Yii::$app instanceof yii\web\Application && Yii::$app->user->getIdentity(false) instanceof static;
    }
}
