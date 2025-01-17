<?php

namespace common\models\reference;

use backend\widgets\ActiveField;
use common\models\enum\UserType;
use Throwable;
use yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * Модель спарвочника "Пользователи"
 *
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $name
 * @property string $name_full
 * @property integer $user_type_id
 * @property string $password_reset_token
 * @property string $verification_token
 */
class User extends Reference implements IdentityInterface
{
    /**
     * @var string
     */
    public $password;

    /**
     * @var boolean
     */
    public $is_password_block = true;

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

    public function __toString()
    {
        return isset($this->name_full) ? $this->name_full : $this->name;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], 'unique', 'message' => 'Это имя пользователя уже занято.'],
            [['email'], 'filter', 'filter' => 'trim'],
            [['password'], 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            [['email'], 'string', 'max' => 255],
            [['name_full'], 'string', 'max' => 1024],
            [['name_full'], 'filter', 'filter' => 'trim'],
            [['email'], 'unique', 'message' => 'Этот адрес электронной почты уже занят.'],
            [['user_type_id'], 'integer'],
            [['is_password_block'], 'boolean'],
            [['name', 'user_type_id'], 'required']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Логин',
            'name_full' => 'Полное имя',
            'password' => 'Пароль',
            'email' => 'Email',
            'user_type_id' => 'Тип пользователя',
            'is_password_block' => 'Блокировка смены пароля',
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
        return static::find()->andWhere(['LOWER(email)' => mb_strtolower($email)])->one();
    }

    /**
     * Finds active user by email
     *
     * @param string $email
     * @return array|User|yii\db\ActiveRecord
     * @throws yii\base\InvalidConfigException
     */
    public static function findActiveByEmail($email)
    {
        return static::find()->active()->andWhere(['LOWER(email)' => mb_strtolower($email)])->one();
    }

    /**
     * Finds user by name
     *
     * @param string $name
     * @return array|User|null
     * @throws yii\base\InvalidConfigException
     */
    public static function findByName($name)
    {
        return static::find()->andWhere(['name' => $name])->one();
    }

    /**
     * Finds active user by name
     *
     * @param string $name
     * @return array|User|null
     * @throws yii\base\InvalidConfigException
     */
    public static function findActiveByName($name)
    {
        return static::find()->active()->andWhere(['name' => $name])->one();
    }

    /**
     * Finds user by name or email
     *
     * @param $login
     * @return array|yii\db\ActiveRecord|null
     * @throws yii\base\InvalidConfigException
     */
    public static function findByNameOrEmail($login)
    {
        return static::find()->active()->andWhere(['OR', ['name' => $login], ['email' => $login]])->one();
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
     * @return ActiveQuery
     */
    public function getUserType()
    {
        return $this->hasOne(UserType::class, ['id' => 'user_type_id']);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password = $password;
        $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
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
     * Проверка является ли пользователь родителем
     */
    static public function isFather()
    {
        return Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::FATHER;
    }

    /**
     * Проверка является ли пользователь сотрудником
     */
    static public function isEmployee()
    {
        return Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::FATHER;
    }

    /**
     * Проверка является ли пользователь поставщиком
     */
    static public function isProductProvider()
    {
        return Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::PRODUCT_PROVIDER;
    }

    /**
     * Проверка является ли пользователь объектом обслуживания
     */
    static public function isServiceObject()
    {
        return Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::SERVICE_OBJECT;
    }

    /**
     * Получение профиля по типу пользователя
     * @return Employee|Father|ProductProvider|ServiceObject|null
     */
    public function getProfile()
    {
        switch ($this->user_type_id) {
            case UserType::SERVICE_OBJECT:
                return ServiceObject::findOne(['user_id' => $this->id]);
            case UserType::EMPLOYEE:
                return Employee::findOne(['user_id' => $this->id]);
            case UserType::FATHER:
                return Father::findOne(['user_id' => $this->id]);
            case UserType::PRODUCT_PROVIDER:
                return ProductProvider::findOne(['user_id' => $this->id]);
        }
        return null;
    }

    /**
     * @inheritdoc
     * @throws yii\base\Exception
     * @throws \Exception
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            if ($this->isNewRecord) {
                if (!$this->auth_key) {
                    $this->auth_key = Yii::$app->security->generateRandomString();
                }
            }
            if (!$this->is_password_block && !empty($this->password)) {
                $this->setPassword($this->password);
            }
        }
        return $parentResult;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (array_key_exists('user_type_id', $changedAttributes)
            && $this->user_type_id != $changedAttributes['user_type_id']) {
            $auth = Yii::$app->authManager;
            $role = self::checkRole($changedAttributes['user_type_id']);
            if ($role) {
                $role = $auth->getRole($role);
                $auth->revoke($role, $this->id);
            }
            $role = self::checkRole($this->user_type_id);
            if ($role) {
                $role = $auth->getRole($role);
                $auth->assign($role, $this->id);
            }
        }
    }

    /**
     * Проверка какой ролью должен обладать пользователь по его типу
     * @param integer $userTypeId
     * @return string
     */
    public static function checkRole($userTypeId)
    {
        switch ($userTypeId) {
            case UserType::ADMIN:
                return 'super-admin';
            case UserType::OTHER;
                return 'other';
            case UserType::SERVICE_OBJECT;
                return 'service-object';
            case UserType::EMPLOYEE;
                return 'employee';
            case UserType::FATHER;
                return 'father';
            case UserType::PRODUCT_PROVIDER:
                return 'product-provider';
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            if ($this->scenario != self::SCENARIO_SEARCH) {
                $this->_fieldsOptions['name_full']['displayType'] = ActiveField::READONLY;
            }
            $this->_fieldsOptions['password']['displayType'] = ActiveField::PASSWORD_READONLY;
            $this->_fieldsOptions['is_password_block']['displayType'] = ActiveField::BOOL;
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
     * @throws Throwable
     */
    public static function isBackendUser()
    {
        return Yii::$app instanceof yii\web\Application && Yii::$app->user->getIdentity(false) instanceof static;
    }
}
