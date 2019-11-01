<?php

namespace backend\controllers;

use common\models\form\Form;
use backend\widgets\ActiveField;
use backend\widgets\ActiveForm;
use BadMethodCallException;
use common\helpers\ArrayHelper;
use common\models\ActiveRecord;
use common\models\cross\CrossTable;
use common\models\tablepart\TablePart;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Базовый класс контроллера для моделей
 */
abstract class BackendModelController extends Controller
{
    /**
     * @var string имя класса модели
     */
    public $modelClass;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass не указан.');
        }
        if (!is_subclass_of($this->modelClass, ActiveRecord::class, true) &&
            !is_subclass_of($this->modelClass, Form::class, true)) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass не является подклассом ' .
                ActiveRecord::class . '.');
        }
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/base/index',
            ],
            'create' => [
                'class' => 'backend\actions\base\CreateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/base/update',
            ],
            'update' => [
                'class' => 'backend\actions\base\UpdateAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/base/update',
            ],
            'view' => [
                'class' => 'backend\actions\base\ViewAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/base/view',
            ],
            'delete' => [
                'class' => 'backend\actions\base\DeleteAction',
                'modelClass' => $this->modelClass,
            ],
            'delete-checked' => [
                'class' => 'backend\actions\base\DeleteCheckedAction',
                'modelClass' => $this->modelClass,
            ],
            'search' => [
                'class' => 'backend\actions\base\SearchAction',
                'modelClass' => $this->modelClass,
                'searchFields' => ['id'],
            ],
            'select' => [
                'class' => 'backend\actions\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/base/select',
            ],
            'restore' => [
                'class' => 'backend\actions\base\RestoreAction',
                'modelClass' => $this->modelClass,
            ],
        ]);
    }

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
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['super-admin'],
                    ],
                    [
                        'actions' => ['search', 'select'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['super-admin'],
                    ],
                    [
                        'actions' => ['update'],
                        'allow' => true,
                        'roles' => ['super-admin'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['super-admin'],
                    ],
                    [
                        'actions' => ['delete', 'delete-checked'],
                        'allow' => true,
                        'roles' => ['super-admin'],
                    ],
                    [
                        'actions' => ['restore'],
                        'allow' => true,
                        'roles' => ['super-admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-checked' => ['POST'],
                ],
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'only' => ['delete-checked', 'search'],
                'formats' => ['application/json' => Response::FORMAT_JSON],
            ],
        ]);
    }

    /**
     * Создание новой модели
     * @param string|null $modelClass
     * @return ActiveRecord
     */
    public function createModel($modelClass = null)
    {
        /* @var ActiveRecord $modelClass */
        $modelClass = $modelClass ? $modelClass : $this->modelClass;
        return new $modelClass();
    }

    /**
     * Поиск модели по ее ID
     * @param string|integer $id
     * @param string|null $modelClass
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findModel($id, $modelClass = null)
    {
        /* @var ActiveRecord $modelClass */
        $modelClass = $modelClass ? $modelClass : $this->modelClass;
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $modelClass::findOne(array_combine($keys, $values));
            }
        } elseif ($id !== null) {
            $model = $modelClass::findOne($id);
        }
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('Элемент с указанным ID не найден: ' . $id);
        }
    }

    /**
     * Вывод представления в разных шаблонах в зависимости от типа запроса
     * @param string $view
     * @param array $params
     * @return string
     */
    public function renderUniversal($view, $params = [])
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax($view, $params);
        } else {
            $layout = Yii::$app->request->get('layout', false);
            if ($layout) {
                $this->layout = $layout;
            }
            return $this->render($view, $params);
        }
    }

    /**
     * Перенаправление с учетом настроек, заданных в $_GET
     * @param array|string $url
     * @param int $statusCode
     * @return Response
     */
    public function autoRedirect($url, $statusCode = 302)
    {
        $newUrl = Yii::$app->request->get('redirect', false);
        if ($newUrl) {
            $url = $newUrl;
        } else if (is_array($url) && ($layout = Yii::$app->request->get('layout', false))) {
            $url['layout'] = $layout;
        }
        return $this->redirect($url, $statusCode);
    }

    /**
     * Получение списка вкладок для формы редактирования
     * @param ActiveRecord $model
     * @return array
     */
    public function getTabs($model)
    {
        $result = [];
        foreach ($model->getTableParts() as $relation => $relationClass) {
            $result[$relation] = [
                'label' => $model->getAttributeLabel($relation) . ' (' . count($model->{$relation}) . ')',
                'viewUpdate' => '@backend/views/base/_tablePartUpdate',
                'viewView' => '@backend/views/base/_tablePartView',
                'params' => [
                    'relation' => $relation,
                    'relationClass' => $relationClass,
                ],
            ];
        }
        return $result;
    }

    /**
     * Получение списка колонок для отображения табличной части в форме редактирования объекта
     * @param ActiveRecord $model
     * @param string $tablePartRelation
     * @param ActiveForm $form
     * @param bool $readonly
     * @return array список колонок
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    static public function getTablePartColumns($model, $tablePartRelation, $form, $readonly = false)
    {
        $tableParts = $model->getTableParts();
        if (!isset($tableParts[$tablePartRelation])) {
            throw new BadMethodCallException();
        }
        $tablePartClass = $tableParts[$tablePartRelation];
        /** @var TablePart $tablePartModel */
        $tablePartModel = new $tablePartClass();
        $tablePartModel->parent_id = $model->id;
        $result = [];
        foreach ($tablePartModel->activeAttributes() as $attribute) {
            if ($attribute == 'parent_id') {
                continue;
            }
            $fieldOptions = $tablePartModel->getFieldOptions($attribute);
            $headerOptions = [];
            if (in_array($fieldOptions['type'], [ActiveField::REFERENCE, ActiveField::MULTI_REFERENCE])) {
                $headerOptions['style'] = 'width:400px;';
            } else if (in_array($fieldOptions['type'], [ActiveField::ENUM])) {
                $headerOptions['style'] = 'width:200px;';
            } else if (in_array($fieldOptions['type'], [ActiveField::INT, ActiveField::FLOAT, ActiveField::DECIMAL, ActiveField::MONEY])) {
                $headerOptions['style'] = 'width:120px;';
            }
            $result[$attribute] = [
                'attribute' => $attribute,
                'label' => $tablePartModel->getAttributeLabel($attribute),
                'headerOptions' => $headerOptions,
                'format' => 'raw',
                'value' => function ($tablePartRow) use ($form, $model, $tablePartRelation, $attribute, $readonly) {
                    /** @var TablePart $tablePartRow */
                    if ($readonly) {
                        $result = $form->field($tablePartRow, $attribute, [
                            'template' => "{input}\n{hint}\n{error}",
                            'inputOptions' => [
                                'id' => Html::getInputId($model, '[' . $tablePartRelation . '][' . $tablePartRow->id . ']' . $attribute),
                                'name' => Html::getInputName($model, '[' . $tablePartRelation . '][' . $tablePartRow->id . ']' . $attribute),
                                'class' => 'form-control',
                            ],
                        ])->readonly();
                    } else {
                        $result = $form->autoField($tablePartRow, $attribute, [
                            'template' => "{input}\n{hint}\n{error}",
                            'inputOptions' => [
                                'id' => Html::getInputId($model, '[' . $tablePartRelation . '][' . $tablePartRow->id . ']' . $attribute),
                                'name' => Html::getInputName($model, '[' . $tablePartRelation . '][' . $tablePartRow->id . ']' . $attribute),
                                'class' => 'form-control',
                                'label' => '',
                            ],
                        ]);
                    }
                    return $result;
                },
            ];
        }
        return $result;
    }

    /**
     * Получение списка колонок для отображения списка связанных записей в форме редактирования объекта
     * @param ActiveRecord $model
     * @param string $crossTableRelation
     * @param string $crossTableRelationClass
     * @param string $parentAttribute
     * @param ActiveForm $form
     * @param boolean $isExternalEdit
     * @return array список колонок
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    public function getCrossTableColumns($model, $crossTableRelation, $crossTableRelationClass, $parentAttribute, $form, $isExternalEdit = false)
    {
        /** @var CrossTable $crossTableRowModel */
        $crossTableRowModel = new $crossTableRelationClass();
        $result = [];
        $attributesWithRelation = $crossTableRowModel->getAttributesWithRelation();
        foreach ($crossTableRowModel->activeAttributes() as $attribute) {
            if ($attribute == $parentAttribute) {
                continue;
            }
            $fieldOptions = $crossTableRowModel->getFieldOptions($attribute);
            $headerOptions = [];
            if (in_array($fieldOptions['type'], [ActiveField::REFERENCE, ActiveField::MULTI_REFERENCE, ActiveField::ENUM])) {
                $headerOptions['style'] = 'width:300px;';
            }
            if ($isExternalEdit) {
                if ($crossTableRowModel->hasAttribute($attribute) && !$crossTableRowModel->isAttributeSafe($attribute)) {
                    continue;
                }
                $fieldOptions = $crossTableRowModel->getFieldOptions($attribute);
                if (!$fieldOptions || $fieldOptions['type'] == ActiveField::IGNORE) {
                    continue;
                }
                $result[$attribute] = [
                    'attribute' => $attribute,
                    'label' => $crossTableRowModel->getAttributeLabel($attribute),
                    'headerOptions' => $headerOptions,
                    'format' => 'raw',
                    'value' => function ($crossTableRow) use ($attribute, $fieldOptions, $attributesWithRelation) {
                        /** @var CrossTable $crossTableRow */
                        switch ($fieldOptions['displayType']) {
                            case ActiveField::BOOL:
                                $result = Yii::$app->formatter->format($crossTableRow->{$attribute}, 'boolean');
                                break;
                            case ActiveField::PASSWORD:
                                $result = '******';
                                break;
                            default:
                                if (isset($attributesWithRelation[$attribute])) {
                                    $attribute = $attributesWithRelation[$attribute]['name'];
                                }
                                $value = $crossTableRow->{$attribute};
                                $result = !is_object($value) && is_array($value) ? implode(', ', $value) : (string)$value;
                        }
                        return $result;
                    },
                ];
            } else {
                $result[$attribute] = [
                    'attribute' => $attribute,
                    'label' => $crossTableRowModel->getAttributeLabel($attribute),
                    'headerOptions' => $headerOptions,
                    'format' => 'raw',
                    'value' => function ($crossTableRow) use ($form, $model, $crossTableRelation, $attribute) {
                        /** @var CrossTable $crossTableRow */
                        return $form->autoField($crossTableRow, $attribute, [
                            'template' => "{input}\n{hint}\n{error}",
                            'inputOptions' => [
                                'id' => Html::getInputId($crossTableRow, '[' . $crossTableRow->id . ']' . $attribute),
                                'name' => Html::getInputName($crossTableRow, '[' . $crossTableRow->id . ']' . $attribute),
                                'class' => 'form-control',
                            ],
                        ]);
                    },
                ];
            }
        }
        return $result;
    }

    /**
     * Вывод поля формы с автоопределением типа поля
     * @param ActiveRecord $model
     * @param ActiveRecord $filterModel
     * @return array
     * @throws InvalidConfigException
     * @throws \ReflectionException
     */
    public function generateAutoColumns($model, $filterModel)
    {
        $result = [];
        $form = new ActiveForm();
        ob_get_clean();
        $filterFieldsOptions = $filterModel->getFieldsOptions();
        foreach ($model->getFieldsOptions() as $field => $fieldOptions) {
            $originalField = $field;
            if (in_array($fieldOptions['displayType'], [
                ActiveField::IGNORE,
                ActiveField::MULTI_REFERENCE,
                ActiveField::HTML,
                ActiveField::TEXT,
                ActiveField::HIDDEN,
                ActiveField::FILE,
            ])) {
                continue;
            }
            $columnType = 'text';
            if ($fieldOptions['displayType'] == ActiveField::READONLY) {
                $fieldOptions['displayType'] = $fieldOptions['type'];
            }
            if (in_array($fieldOptions['displayType'], [ActiveField::BOOL, ActiveField::EMAIL])) {
                $columnType = $fieldOptions['displayType'];
            } else if (in_array($fieldOptions['displayType'], [ActiveField::ENUM, ActiveField::REFERENCE, ActiveField::CATEGORY])) {
                if ($relationData = $model->getAttributeRelation($field)) {
                    $field = $relationData['name'];
                }
            }
            $filter = false;
            $headerOptions = [];
            $filterOptions = [];
            $filterFieldOptions = isset($filterFieldsOptions[$originalField]) ? $filterFieldsOptions[$originalField] : null;
            if ($filterFieldOptions) {
                if ($filterFieldOptions['displayType'] == ActiveField::BOOL) {
                    $headerOptions = ['style' => 'min-width:150px;'];
                    $filter = $form->field($filterModel, $originalField)->dropDownList(
                        ['' => '(не указано)', '0' => 'Нет', '1' => 'Да']
                    );
                } else {
                    $headerOptions = ['style' => 'min-width:100px;'];
                    $filterOptions = ['style' => 'max-width:200px;'];
                    $filter = $form->autoField($filterModel, $originalField, $filterFieldOptions);
                }
                $filter = $filter ? (string)$filter->label(false) : false;
            }
            $result[$field] = [
                'attribute' => $field,
                'label' => $model->getAttributeLabel($field),
                'format' => $columnType,
                'headerOptions' => $headerOptions,
                'filter' => $filter,
                'filterOptions' => $filterOptions,
            ];
        }
        return $result;
    }
}
