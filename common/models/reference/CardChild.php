<?php

namespace common\models\reference;

use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * Модель справочника "Карты детей"
 *
 * @property string  $card_number_hash
 * @property string  $card_keyword_hash
 * @property float   $balance
 * @property float   $limit_per_day
 * @property integer $child_id
 * @property string  $auth_key
 *
 * Отношения:
 * @property Child   $child
 */
class CardChild extends Reference implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Карта ребёнка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Карты детей';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['card_number_hash', 'card_keyword_hash'], 'string'],
            [['balance', 'limit_per_day'], 'number'],
            [['child_id'], 'integer'],
            [['card_number_hash', 'card_keyword_hash', 'child_id'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'card_number_hash' => 'Номер карты',
            'card_keyword_hash' => 'Кодовое слово',
            'balance' => 'Баланс',
            'limit_per_day' => 'Лимит в день',
            'child_id' => 'Ребёнок',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
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
     * Validates keyword
     *
     * @param string $keyword keyword to validate
     * @return boolean if keyword provided is valid for current user
     * @throws \yii\base\Exception
     */
    public function validateKeyword($keyword)
    {
        return Yii::$app->security->validatePassword($keyword, Yii::$app->security->generatePasswordHash('1234567890')); // заглушка для аутентификации в будущем
    }

    /**
     * @inheritdoc
     */
    public static function getChild($cardNumber)
    {
        $card = self::findOne(['card_number_hash' => $cardNumber]);
        if ($card) {
            $child = Child::findOne(['id' => $card->id]);
            return $child;
        }
        return false;
    }

    /**
     * Finds card by card number
     *
     * @param $cardNumber
     * @return array|yii\db\ActiveRecord|null
     */
    public static function findByCardNumber($cardNumber)
    {
        return static::findOne(['card_number_hash' => $cardNumber]);
    }
}