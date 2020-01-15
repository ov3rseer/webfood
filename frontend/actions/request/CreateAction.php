<?php

namespace frontend\actions\request;

use common\components\DateTime;
use common\models\document\Request;
use common\models\enum\DocumentStatus;
use common\models\enum\RequestStatus;
use common\models\reference\User;
use common\models\tablepart\ProductProviderServiceObject;
use frontend\actions\FrontendModelAction;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\web\Response;

class CreateAction extends FrontendModelAction
{
    /**
     * @var string путь к файлу представления для вкладок
     */
    public $tabsViewPath;

    /**
     * @inheritdoc
     * @param $id
     * @return array|string|Response
     * @throws InvalidConfigException
     * @throws UserException
     * @throws \ReflectionException
     */
    public function run()
    {
        /** @var Request $model */

        $modelForm = $this->controller->createModel($this->modelClassForm);
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        $modelForm->load($requestData);

        $serviceObject = null;
        if (User::isServiceObject()) {
            $serviceObject = Yii::$app->user->identity->getProfile();
            if (!$serviceObject) {
                Yii::$app->session->setFlash('info', 'Вы не являетесь объектом обслуживания.');
                return $this->controller->goHome();
            }
        }
        $productProviderServiceObject = ProductProviderServiceObject::findOne(['service_object_id' => $serviceObject->id]);
        if (!$productProviderServiceObject) {
            Yii::$app->session->setFlash('info', 'Вы не связаны ни с одним из поставщиков.');
            return $this->controller->goHome();
        }

        $model = $this->controller->createModel($this->modelClass);
        $model->date = new DateTime();
        $model->status_id = DocumentStatus::DRAFT;
        $model->request_status_id = RequestStatus::NEW;
        $model->delivery_day = $modelForm->delivery_day;
        $model->product_provider_id = $productProviderServiceObject->parent_id;
        $model->service_object_id = $productProviderServiceObject->service_object_id;
        $model->save();
        Yii::$app->session->setFlash('success', 'Элемент "' . $model . '" успешно создан');
        return $this->controller->autoRedirect('index');
    }
}