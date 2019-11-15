<?php

namespace frontend\controllers\father;

use common\helpers\ArrayHelper;
use common\models\document\RefillBalance;
use common\models\enum\DocumentStatus;
use common\models\reference\CardChild;
use Yii;
use yii\base\UserException;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Контроллер для управления балансом
 */
class MoneyController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['add-money'],
                        'allow' => true,
                        'roles' => ['father'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @return string
     * @throws UserException
     */
    public function actionAddMoney()
    {
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        $money = $requestData['money'];
        $cardId = $requestData['cardId'];
        if ($cardId && $money) {
            $card = CardChild::findOne(['id' => $cardId]);
            if ($card) {
                $refillBalance = new RefillBalance();
                $refillBalance->status_id = DocumentStatus::POSTED;
                $refillBalance->card_id = $cardId;
                $refillBalance->sum = $money;
                $refillBalance->save();
            }
        }
        return true;
    }
}