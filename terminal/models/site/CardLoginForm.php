<?php
namespace terminal\models\site;

use common\models\reference\CardChild;
use common\models\reference\Child;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

class CardLoginForm extends Model
{
    public $cardNumber;
    public $keyword = '1234567890'; // заглушка для аутентификации в будущем

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['cardNumber'/*, 'keyword'*/], 'required'],
            // password is validated by validatePassword()
            ['keyword', 'validateKeyword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'cardNumber' => 'Номер карты',
            'keyword' => 'Кодовое слово',
        ]);
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     * @param $attribute
     * @throws \yii\base\Exception
     */
    public function validateKeyword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validateKeyword($this->keyword)) {
                $this->addError($attribute, 'Неверное кодовое слово.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return array|CardChild|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = CardChild::findByCardNumber($this->cardNumber);
        }
        return $this->_user;
    }
}
