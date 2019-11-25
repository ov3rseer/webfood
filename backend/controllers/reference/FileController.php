<?php

namespace backend\controllers\reference;

use common\helpers\ArrayHelper;
use common\models\reference\User;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * Контроллер для справочника "Файлы"
 */
class FileController extends ReferenceController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'common\models\reference\File';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/file/index',
            ],
            'create' => [
                'class' => 'backend\actions\reference\file\CreateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/file/update',
            ],
            'update' => [
                'class' => 'backend\actions\reference\file\UpdateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/reference/file/update',
            ],
            'search' => [
                'class' => 'backend\actions\reference\file\SearchAction',
                'modelClass' => $this->modelClass,
                'searchFields' => ['name', 'name_full'],
            ],
            'download' => [
                'class' => 'backend\actions\reference\file\DownloadAction',
                'modelClass' => $this->modelClass,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $result = ArrayHelper::merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => ['application/json' => Response::FORMAT_JSON],
                'only' => ['generate-url'],
            ],
            'access' => [
                'rules' => [
                    [
                        'actions' => ['download'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);
        if (Yii::$app->request->getAuthUser()) {
            $result = ArrayHelper::merge([
                'basicAuth' => [
                    'class' => HttpBasicAuth::className(),
                    'auth' => function ($username, $password) {
                        /** @var User $user */
                        $user = User::find()->active()->andWhere(['LOWER(name)' => mb_strtolower($username)])->one();
                        if ($user->validatePassword($password)) {
                            return $user;
                        }
                        return null;
                    },
                ],
            ], $result);
        }
        return $result;
    }

    //Нужно починить предпросмотр, ругается на длинные в ширину картинки
//    /**
//     * @inheritdoc
//     */
//    public function generateAutoColumns($model, $filterModel)
//    {
//        return array_merge([
//            'preview' => [
//                'label' => 'Предпросмотр',
//                'headerOptions' => ['style' => 'min-width:200px;'],
//                'format' => 'raw',
//                'value' => function($row) {
//                    /** @var File $row */
//                    $result = '';
//                    if ($row->isImage()) {
//                        $imgOptions = ['width' => 200, 'height' => 150];
//                        $thumbImage = $row->publish(false, $imgOptions);
//                        $result .= Html::img($thumbImage, $imgOptions) . '<br>';
//                    }
//                    $result .= Html::a('Скачать', ['/reference/file/download', 'id' => $row->id],
//                        ['target' => '_blank', 'data-pjax' => 0]);
//                    return $result;
//                },
//            ],
//        ], parent::generateAutoColumns($model, $filterModel));
//    }
}
