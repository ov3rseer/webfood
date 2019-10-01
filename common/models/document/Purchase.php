<?php

namespace common\models\document;

use backend\controllers\document\DocumentController;
use backend\widgets\ActiveForm;
use common\models\enum\DocumentStatus;
use common\models\reference\CardChild;
use common\models\register\registerAccumulate\CardHistory;
use common\models\tablepart\PurchaseComplex;
use common\models\tablepart\PurchaseMeal;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * Модель документа "Покупка"
 *
 * Свойства:
 * @property integer $card_id
 * @property float $sum
 *
 * Отношения:
 * @property PurchaseComplex[] $purchaseComplexes
 * @property PurchaseMeal[] $purchaseMeals
 * @property CardChild $card
 */
class Purchase extends Document
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Покупка';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Покупки';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['card_id'], 'integer'],
            [['sum'], 'number', 'min' => 0],
            [['card_id'], 'required'],
            [['status_id'], 'validateStatus'],
        ]);
    }

    /**
     * Запрет на повторное проведение документа
     */
    public function validateStatus()
    {
        if (!$this->isNewRecord && $this->oldAttributes['status_id'] == DocumentStatus::POSTED) {
            $this->addError('summary', 'Пополнение счёта уже проведёно, невозможно изменить данные');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'card_id' => 'Карта',
            'sum' => 'Сумма',
            'purchaseMeals' => 'Купленные блюда',
            'purchaseComplexes' => 'Купленные комплексы',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(CardChild::class, ['id' => 'card_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPurchaseComplexes()
    {
        return $this->hasMany(PurchaseComplex::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @return ActiveQuery
     */
    public function getPurchaseMeals()
    {
        return $this->hasMany(PurchaseMeal::class, ['parent_id' => 'id'])
            ->orderBy('id ASC');
    }

    /**
     * @inheritdoc
     */
    public function getTableParts()
    {
        return array_merge([
            'purchaseMeals' => PurchaseMeal::class,
            'purchaseComplexes' => PurchaseComplex::class,
        ], parent::getTableParts());
    }

    /**
     * @inheritdoc
     * @param $tablePartRelation
     * @param $form
     * @param bool $readonly
     * @return array
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    public function getTablePartColumns($tablePartRelation, $form, $readonly = false)
    {
        /** @var ActiveForm $form */
        $model = $this;
        $parentResult = DocumentController::getTablePartColumns($model, $tablePartRelation, $form, $readonly);
        if (in_array($tablePartRelation, ['purchaseMeals', 'purchaseComplexes'])) {
            $parentResult['price'] = [
                'format' => 'raw',
                'label' => 'Цена',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) use ($form, $model, $tablePartRelation) {
                    /** @var PurchaseMeal $rowModel */
                    $result = '';
                    $parameter = $tablePartRelation == 'purchaseMeals' ? 'meal' : 'complex';
                    if (!$rowModel->isNewRecord && isset($rowModel->{$parameter}->price)) {
                        $result = Html::textInput(
                            Html::getInputName($model, '[' . $tablePartRelation . '][' . $rowModel->id . ']price'),
                            Html::encode($rowModel->{$parameter}->price),
                            [
                                'id' => Html::getInputId($model, '[' . $tablePartRelation . '][' . $rowModel->id . ']price'),
                                'class' => 'form-control',
                                'readonly' => true
                            ]);
                    }
                    return $result;
                }
            ];
            $parentResult['sum'] = [
                'format' => 'raw',
                'label' => 'Сумма',
                'headerOptions' => ['style' => 'text-align:center;'],
                'value' => function ($rowModel) use ($form, $model, $tablePartRelation) {
                    /** @var PurchaseMeal $rowModel */
                    $result = 0;
                    $parameter = $tablePartRelation == 'purchaseMeals' ? 'meal' : 'complex';
                    if (!$rowModel->isNewRecord && isset($rowModel->{$parameter}->price)) {
                        $result = $rowModel->{$parameter}->price * $rowModel->quantity;
                    }
                    return number_format($result, 2);
                }
            ];
        }
        return $parentResult;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsForDependentRegisters()
    {
        return [
            // История карты
            CardHistory::className() => [
                'balance_error' => function () {
                    $balanceRow = CardHistory::findBalance(null, ['sum'], [], 't')
                        ->andWhere(['t.card_id' => $this->card_id])
                        ->one();
                    if (empty($balanceRow['sum'])) {
                        $balanceRow['sum'] = 0;
                    }
                    $result = $balanceRow['sum'] - $this->sum;
                    if ($result < 0) {
                        return 'Отрицательный баланс.';
                    }
                    return '';
                },
                'function' => function (Purchase $documentModel) {
                    $result = [];
                    if ($documentModel) {
                        $result[] = [
                            'card_id' => $documentModel->card_id,
                            'sum' => -1 * $documentModel->sum,
                        ];
                    }
                    return $result;
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $parentResult = parent::beforeSave($insert);
        if ($parentResult) {
            $sum = 0;
            foreach ($this->purchaseMeals as $purchaseMeal) {
                if (isset($purchaseMeal->meal)) {
                    $sum += $purchaseMeal->quantity * $purchaseMeal->meal->price;
                }
            }
            foreach ($this->purchaseComplexes as $purchaseComplex) {
                if (isset($purchaseComplex->complex)) {
                    $sum += $purchaseComplex->quantity * $purchaseComplex->complex->price;
                }
            }
            $this->sum = $sum;
        }
        return $parentResult;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->status_id == DocumentStatus::POSTED) {
            $cardBalance = CardHistory::findBalance(null, ['sum'], ['card_id'], 't')->andWhere(['card_id' => $this->card_id])->one();
            if (!empty($cardBalance['sum'] && $this->card)) {
                $this->card->balance = $cardBalance['sum'];
                $this->card->save();
            }
        }
    }
}