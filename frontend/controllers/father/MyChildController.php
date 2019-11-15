<?php

namespace frontend\controllers\father;

use common\helpers\ArrayHelper;
use common\models\enum\UserType;
use common\models\reference\Child;
use common\models\reference\Father;
use common\models\reference\SchoolClass;
use common\models\reference\ServiceObject;
use common\models\tablepart\FatherChild;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\bootstrap\Html;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Контроллер для управления детьми
 */
class MyChildController extends Controller
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
                        'actions' => ['index', 'add-child', 'delete-child', 'search-child'],
                        'allow' => true,
                        'roles' => ['father'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $result = parent::actions();
        unset($result['index']);
        return $result;
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $father = null;
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (Yii::$app->request->isAjax && isset($requestData['cardId'])) {
            return $this->renderAjax('_cardHistory', ['cardId' => $requestData['cardId']]);
        }
        if (Yii::$app->user) {
            /** @var Father $father */
            $father = Father::findOne(['user_id' => Yii::$app->user->id]);
            if ($father === null) {
                throw new NotFoundHttpException('Вы не являетесь родителем, чтобы просматривать данную страницу.');
            }
        }
        return $this->render('index', ['father' => $father]);
    }

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function actionSearchChild()
    {
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        $userInput = $requestData['userInput'];
        $list = [];
        if ($userInput) {
            $search = explode(' ', $userInput);
            $sql = ['OR'];
            foreach ($search as $word) {
                $sql[] = ['ilike', 'c.surname', $word];
                $sql[] = ['ilike', 'c.forename', $word];
                $sql[] = ['ilike', 'c.patronymic', $word];
            }

            $childrenQuery = Child::find()
                ->alias('c')
                ->select(['concat(c.name_full, \', \',sc.name, \', \', so.name) as name'])
                ->innerJoin(ServiceObject::tableName() . ' AS so', 'so.id = c.service_object_id')
                ->innerJoin(SchoolClass::tableName() . ' AS sc', 'sc.id = c.school_class_id')
                ->andWhere(['c.is_active' => true])
                ->filterWhere($sql)
                ->indexBy('id')
                ->asArray()
                ->column();

            foreach ($childrenQuery as $childId => $childName) {
                $list[] = ['value' => $childName, 'id' => $childId];
            }
        }
        $result = Html::beginTag('div', ['class' => 'list-group']);
        if (!empty($list)) {
            foreach ($list as $key => $item) {
                $result .= Html::a($item['value'], '#', ['class' => 'list-group-item', 'data' => ['child-id' => $item['id']]]);
            }
        } else {
            $result .= Html::a('Ничего не найдено!', '#', ['class' => 'list-group-item']);
        }
        $result .= Html::endTag('div');
        return $result;
    }

    /**
     * @return string
     * @throws UserException
     */
    public function actionAddChild()
    {
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (!empty($requestData['childId'])) {
            $child = Child::findOne(['id' => $requestData['childId']]);
            if (!$child) {
                return 'Ребёнок не найден.';
            }
            $father = null;
            if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::FATHER) {
                /** @var Father $father */
                $father = Father::findOne(['user_id' => Yii::$app->user->id]);
            }
            if (!$father) {
                return 'Вы не являетесь родителем.';
            }
            $fatherChild = FatherChild::findOne(['parent_id' => $father->id, 'child_id' => $child->id]);
            if (!$fatherChild) {
                $fatherChild = new FatherChild();
                $fatherChild->parent_id = $father->id;
                $fatherChild->child_id = $child->id;
                $fatherChild->save();
                return true;
            } else {
                return 'Ребенок уже находится в списке ваших детей.';
            }
        }
        return 'Вы не выбрали ребёнка.';
    }

    /**
     * @return string
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDeleteChild()
    {
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (!empty($requestData['childId'])) {
            $father = null;
            if (Yii::$app->user && Yii::$app->user->identity->user_type_id == UserType::FATHER) {
                /** @var Father $father */
                $father = Father::findOne(['user_id' => Yii::$app->user->id]);
            }
            if (!$father) {
                return 'Вы не являетесь родителем.';
            }
            $fatherChild = FatherChild::findOne(['parent_id' => $father->id, 'child_id' => $requestData['childId']]);
            if ($fatherChild) {
                $fatherChild->delete();
                return true;
            } else {
                return 'Этого ребенка не существует в списке ваших детей.';
            }
        }
        return false;
    }
}