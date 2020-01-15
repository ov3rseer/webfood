<?php

namespace frontend\actions\request;

use backend\widgets\ActiveForm;
use common\models\document\Request;
use common\models\enum\RequestStatus;
use common\models\reference\User;
use frontend\actions\FrontendModelAction;
use frontend\models\serviceObject\RequestForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Response;

class IndexAction extends FrontendModelAction
{
    /**
     * @return array|string
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function run()
    {
        /** @var RequestForm $modelForm */
        $modelForm = new $this->controller->modelClassForm();
        $serviceObject = null;
        $requestData = array_merge(Yii::$app->request->post(), Yii::$app->request->get());
        if (Yii::$app->request->isAjax && !Yii::$app->request->isPjax && $modelForm->load($requestData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modelForm);
        }
        if (User::isServiceObject()) {
            $serviceObject = Yii::$app->user->identity->getProfile();
            if (!$serviceObject) {
                Yii::$app->session->setFlash('info', 'Вы не являетесь объектом обслуживания.');
                return $this->controller->goHome();
            }
        }
        $allRequests = Request::find()->andWhere(['service_object_id' => $serviceObject->id])->with('requestProducts');
        $newRequests = clone $allRequests;
        $reservedRequests = clone $allRequests;
        $inRouteRequests = clone $allRequests;
        $deliveredRequests = clone $allRequests;
        $requests[] = ['Все заявки', $allRequests];
        $requests[] = ['Новые', $newRequests->andWhere(['request_status_id' => RequestStatus::NEW])];
        $requests[] = ['Забронированные', $reservedRequests->andWhere(['request_status_id' => RequestStatus::RESERVED])];
        $requests[] = ['В пути', $inRouteRequests->andWhere(['request_status_id' => RequestStatus::IN_ROUTE])];
        $requests[] = ['Доставлены', $deliveredRequests->andWhere(['request_status_id' => RequestStatus::DELIVERED])];
        $columns = $modelForm->getColumns();
        return $this->controller->renderUniversal('@frontend/views/service-object/request/index', ['model' => $modelForm, 'requests' => $requests, 'columns' => $columns]);
    }
}