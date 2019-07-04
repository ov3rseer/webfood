<?php

namespace common\models;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\helpers\StringHelper;
use common\models\tablepart\TablePart;
use common\queries\ActiveQuery;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use yii;
use yii\base\Exception;
use yii\base\UserException;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\helpers\Inflector;
use yii\web\UploadedFile;

/**
 * Родительский класс для всех AR-моделей
 *
 * @property integer $id ID модели
 */
abstract class ActiveRecord extends yii\db\ActiveRecord
{
    /**
     * @var string префикс таблицы
     */
    protected static $tablePrefix;

    /**
     * @var array массив настроек полей модели
     */
    protected $_fieldsOptions = [];

    /**
     * @var array массив атрибутов с отношениями
     */
    protected $_attributesWithRelations;

    /**
     * Сценарий для поиска моделей в журналах
     */
    const SCENARIO_SEARCH = 'search';

    /**
     * Сценарий для служебных операций над данными
     */
    const SCENARIO_SYSTEM = 'system';

    /**
     * Получение имени сущности в единственном числе
     * @return string
     */
    public function getSingularName()
    {
        return '';
    }

    /**
     * Получение имени сущности во множественном числе
     * @return string
     */
    public function getPluralName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $result = parent::scenarios();
        $result[self::SCENARIO_SEARCH] = $result[self::SCENARIO_DEFAULT];
        $result[self::SCENARIO_SEARCH][] = 'id';
        $result[self::SCENARIO_SYSTEM] = [];
        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%' . static::$tablePrefix . Inflector::camel2id(StringHelper::basename(get_called_class()), '_') . '}}';
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        $result = [];
        foreach ($this->scenarios() as $scenario => $value) {
            $result[$scenario] = self::OP_ALL;
        }
        return $result;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function __get($name)
    {
        $schema = Yii::$app->db->schema;
        $columns = $this->getDb()->getTableSchema($this->tableName())->columns;
        if (isset($columns[$name])) {
            $column = $columns[$name];
            if (strpos($column->type, 'date') !== false || strpos($column->type, 'timestamp') !== false) {
                $value = parent::__get($name);
                if (is_null($value)) {
                    return null;
                } elseif (strtotime($value) !== false) {
                    return new DateTime($value, $column->type != $schema::TYPE_DATE);
                }
            }
        }
        return parent::__get($name);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $result = parent::fields();
        $columns = $this->getDb()->getTableSchema($this->tableName())->columns;
        foreach ($result as $key => $value) {
            if (isset($columns[$key])) {
                $column = $columns[$key];
                if (strpos($column->type, 'date') !== false || strpos($column->type, 'timestamp') !== false) {
                    $result[$key] = function($model) use ($key) {
                        return (string)$model->$key;
                    };
                }
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id' => 'ID',
        ]);
    }

    /**
     * Получение публичных свойств класса
     * @return array
     * @throws ReflectionException
     */
    public function getPublicProperties()
    {
        $class = new ReflectionClass($this);
        $result = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $result[] = $property->getName();
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     * @throws ReflectionException
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), $this->getPublicProperties());
    }

    /**
     * @inheritdoc
     * @throws ReflectionException
     */
    public function generateAttributeLabel($name)
    {
        $newName = Inflector::camel2id($name, '_') . '_id';
        $attributesWithRelation = $this->getAttributesWithRelation();
        foreach ($attributesWithRelation as $attribute => $relationData) {
            if ($relationData['name'] == $name) {
                $newName = $attribute;
                break;
            }
        }
        $labels = $this->attributeLabels();
        return isset($labels[$newName]) ? $labels[$newName] : parent::generateAttributeLabel($name);
    }

    /**
     * @inheritdoc
     * @param bool $isStrict строгое сравнение атрибутов модели
     */
    public function getDirtyAttributes($names = null, $isStrict = true)
    {
        $result = parent::getDirtyAttributes($names);
        if (!$isStrict) {
            $oldAttributes = $this->oldAttributes;
            foreach ($result as $attribute => $newValue) {
                if (array_key_exists($attribute, $oldAttributes) && $oldAttributes[$attribute] == $newValue) {
                    unset($result[$attribute]);
                }
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     * @throws UserException
     */
    public function save($runValidation = true, $attributes = null, $onlyIfChanged = false)
    {
        if ($onlyIfChanged && !$this->getDirtyAttributes(null, false)) {
            // Если при нестрогом сравнении не осталось изменившихся реквизитов, то не сохраняем модель
            return true;
        }
        $exception = null;
        try {
            $result = parent::save($runValidation, $attributes);
        } catch (\Exception $ex) {
            $result = false;
            $exception = $ex;
        }
        if (!$result) {
            if ($exception === null) {
                $exceptionMessage = 'Ошибка сохранения';
                if ($this->hasErrors()) {
                    $exceptionMessage = 'Ошибки сохранения:';
                    foreach ($this->getErrors() as $attributeErrors) {
                        foreach ($attributeErrors as $attributeError) {
                            $exceptionMessage .= PHP_EOL . $attributeError;
                        }
                    }
                }
                $exception = new UserException($exceptionMessage);
            } else {
                if ($exception instanceof UserException) {
                    $errorMessage = $exception->getMessage();
                } else {
                    Yii::error((string)$exception);
                    $errorMessage = 'Ошибка сервера';
                }
                if (!in_array($errorMessage, $this->getErrors(''))) {
                    $this->addError('summary', $errorMessage);
                }
            }
            throw $exception;
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $exception = null;
        try {
            $result = parent::delete();
        } catch (\Exception $ex) {
            $result = false;
            $exception = $ex;
        }
        if ($result === false) {
            if ($exception === null) {
                $exception = new UserException('Ошибка удаления');
            } else {
                if ($exception instanceof UserException) {
                    $errorMessage = $exception->getMessage();
                } else {
                    Yii::error((string)$exception);
                    $errorMessage = 'Ошибка сервера';
                }
                if (!in_array($errorMessage, $this->getErrors(''))) {
                    $this->addError('', $errorMessage);
                }
            }
            throw $exception;
        }
        return $result;
    }

    /**
     * Возвращает массив настроек атрибутов модели
     * @return array
     * @throws yii\base\InvalidConfigException
     * @throws ReflectionException
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            $schema = $this->getTableSchema();
            $relations = $this->getAttributesWithRelation();
            $fieldOptions = [];
            foreach ($this->attributes() as $attribute) {
                if (isset($relations[$attribute])) {
//                    if ($relations[$attribute]['class'] == Category::className()) {
//                        $fieldOptions['type'] = ActiveField::CATEGORY;
//                    } else {
//                        $fieldOptions['type'] =
//                            is_subclass_of($relations[$attribute]['class'], Enum::className(), true) ?
//                                ActiveField::ENUM : ActiveField::REFERENCE;
//                    }
                } elseif (($column = $schema->getColumn($attribute)) !== null) {
                    $fieldOptions['type'] = $column->type;
                } else {
                    $fieldOptions['type'] = ActiveField::STRING;
                }
                $fieldOptions['displayType'] = $fieldOptions['type'];
                $this->_fieldsOptions[$attribute] = $fieldOptions;
            }
            foreach ($this->getPublicProperties() as $param) {
                if (!isset($this->_fieldsOptions[$param])) {
                    $this->_fieldsOptions[$param]['displayType'] = ActiveField::STRING;
                }
            }
        }
        return $this->_fieldsOptions;
    }

    /**
     * Возвращает массив настроек атрибута модели
     * @param string $attribute
     * @return array
     * @throws yii\base\InvalidConfigException
     * @throws ReflectionException
     */
    public function getFieldOptions($attribute)
    {
        $fieldsOptions = $this->getFieldsOptions();
        return isset($fieldsOptions[$attribute]) ? $fieldsOptions[$attribute] : [];
    }

    /**
     * Возвращает массив настроек связей атрибутов с другими моделями
     * @return array
     * @throws ReflectionException
     */
    public function getAttributesWithRelation()
    {
        if ($this->_attributesWithRelations === null) {
            $this->_attributesWithRelations = [];
            foreach ($this->attributes() as $attribute) {
                if (substr($attribute, -3) == '_id') {
                    $relation = Inflector::id2camel(substr($attribute, 0, -3), '_');
                    $getter = 'get' . $relation;
                    if (method_exists($this, $getter)) {
                        $query = $this->$getter();
                        if ($query instanceof ActiveQuery) {
                            $this->_attributesWithRelations[$attribute] = [
                                'name'   => lcfirst($relation),
                                'class'  => $query->modelClass,
                                'method' => $getter,
                            ];
                        }
                    }
                }
            }
        }
        return $this->_attributesWithRelations;
    }

    /**
     * Возвращает настройки связи атрибута с другой моделью
     * @param string $attribute
     * @return mixed
     * @throws ReflectionException
     */
    public function getAttributeRelation($attribute)
    {
        $relations = $this->getAttributesWithRelation();
        return isset($relations[$attribute]) ? $relations[$attribute] : false;
    }

    /**
     * Получение списка отношений для табличных частей
     * @return array
     */
    public function getTableParts()
    {
        return [];
    }

    /**
     * @inheritdoc
     * @throws yii\base\InvalidConfigException
     * @throws ReflectionException
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            $fieldsOptions = $this->getFieldsOptions();
            foreach ($fieldsOptions as $field => $fieldOptions) {
                if ($fieldOptions['displayType'] == ActiveField::FILE) {
                    $this->{$field} = UploadedFile::getInstance($this, $field);
                }
                if (in_array($fieldOptions['displayType'], [ActiveField::REFERENCE, ActiveField::ENUM]) && !$this->{$field}) {
                    $this->{$field} = null;
                }
            }
            $scope = ($formName === null ? $this->formName() : $formName);
            if ($scope !== '' && isset($data[$scope])) {
                $data = $data[$scope];
            }
            foreach ($this->getTableParts() as $relationName => $relationClass) {
                $tablePartRows = [];
                if (isset($data[$relationName]) && is_array($data[$relationName])) {
                    foreach ($data[$relationName] as $tablePartRowId => $tablePartRowData) {
                        /** @var TablePart $tablePartRow */
                        $tablePartRow = is_integer($tablePartRowId) ? $relationClass::findOne($tablePartRowId) : null;
                        if (!$tablePartRow) {
                            $tablePartRow = new $relationClass();
                        }
                        $tablePartRow->parent_id = $this->isNewRecord ? StringHelper::generateFakeId() : $this->id;
                        $tablePartRow->id = $tablePartRowId;
                        $tablePartRow->populateRelation('parent', $this);
                        $tablePartRows[$tablePartRow->id] = $tablePartRow;
                    }
                    static::loadMultiple($tablePartRows, $data[$relationName], '');
                }
                $this->populateRelation($relationName, $tablePartRows);
            }
            return true;
        }
        return false;
    }

    /**
     * Возвращает список отношений, которые нужно удалить при удалении текущей модели
     * @return array
     */
    protected function getRelationsForDelete()
    {
        $result = ['before' => [], 'after' => []];
        foreach ($this->getTableParts() as $relationName => $relationClass) {
            $result['before'][] = $relationName;
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        parent::afterValidate();
        foreach ($this->getTableParts() as $relationName => $relationClass) {
            /** @var TablePart[] $tablePartRows */
            $tablePartRows = $this->{$relationName};
            foreach ($tablePartRows as $tablePartRow) {
                // Перезаполняем родителя, чтобы в нем были отражены еще несохраненные изменения
                $tablePartRow->populateRelation('parent', $this);
            }
            static::validateMultiple($tablePartRows);
            foreach ($tablePartRows as $tablePartRow) {
                foreach ($tablePartRow->getErrors() as $attribute => $errors) {
                    // Исключаем ошибки неуказанного поля "parent_id"
                    // для возможности сохранения нового документа сразу с табличной частью
                    if ($attribute != 'parent_id') {
                        foreach ($errors as $error) {
                            $this->addError('[' . $relationName  . '][' . $tablePartRow->primaryKey . ']' . $attribute, $error);
                        }
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     * @param $insert
     * @param $changedAttributes
     * @throws UserException
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        foreach ($this->getTableParts() as $relationName => $relationClass) {
            /** @var TablePart[] $loadedTablePartRows */
            $loadedTablePartRows = $this->{$relationName};
            /** @var TablePart[] $existentTablePartRows */
            $existentTablePartRows = $this->{'get' . $relationName}()->indexBy('id')->all();
            foreach ($loadedTablePartRows as $tablePartRow) {
                if (!is_integer($tablePartRow->id)) {
                    unset($tablePartRow->id);
                } else {
                    unset($existentTablePartRows[$tablePartRow->id]);
                }
                $tablePartRow->parent_id = $this->id;
                $tablePartRow->save();
            }
            foreach ($existentTablePartRows as $existentTablePartRow) {
                    $existentTablePartRow->delete();
            }
        }
        TagDependency::invalidate(Yii::$app->cache, [static::getTagForTable(), static::getTagForRow($this->id)]);
    }

    /**
     * @inheritdoc
     * @throws yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $relations = $this->getRelationsForDelete();
            if (isset($relations['before'])) {
                $this->deleteRelations($relations['before']);
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     * @throws yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $relations = $this->getRelationsForDelete();
        if (isset($relations['after'])) {
            $this->deleteRelations($relations['after']);
        }
        TagDependency::invalidate(Yii::$app->cache, [static::getTagForTable(), static::getTagForRow($this->id)]);
    }

    /**
     * Добавление новой строки в табличную часть
     * @param string $relationName
     */
    public function addNewTablePartRow($relationName)
    {
        $tableParts = $this->getTableParts();
        if (isset($tableParts[$relationName])) {
            /** @var TablePart[] $tablePartRows */
            $tablePartRows = $this->{$relationName};
            $tablePartRow = new $tableParts[$relationName]();
            $tablePartRow->parent_id = $this->id;
            $tablePartRow->id = StringHelper::generateFakeId();
            $tablePartRows[$tablePartRow->id] = $tablePartRow;
            $this->populateRelation($relationName, $tablePartRows);
        }
    }

    /**
     * Получение настроек для заполнения табличной части
     * Для каждого отношения указывается функция для его заполнения.
     * @return array
     *     $relationName => [
     *         'ИмяПравила' => [
     *              'name' => 'Имя правила для отображения пользователю',
     *              'function' => function(Document|Reference $model) { return $model; },
     *          ],
     *         ...
     *     ]
     */
    public function getRulesForProcessingTableParts()
    {
        return [];
    }

    /**
     * Перезаполнение табличной части документа по указанному правилу
     * @param string $relationName отношение табличной части
     * @param string $rule имя правила заполнения
     * @return void
     * @throws Exception
     */
    public function processTablePartByRule($relationName, $rule)
    {
        $rulesForProcessingTableParts = $this->getRulesForProcessingTableParts();
        if (!isset($rulesForProcessingTableParts[$relationName]) ||
            !isset($rulesForProcessingTableParts[$relationName][$rule]['function']) ||
            !is_callable($rulesForProcessingTableParts[$relationName][$rule]['function'])) {
            throw new Exception('Ошибка в ActiveRecord::processTablePartByRule(). Неизвестное правило заполнения табличной части "' . $relationName . '": '.$rule);
        }
        $ruleFunction = $rulesForProcessingTableParts[$relationName][$rule]['function'];
        call_user_func($ruleFunction, $this);
    }

    /**
     * Удаление связанных отношений модели
     * @param array $relations
     * @throws yii\db\StaleObjectException
     * @throws \Throwable
     */
    protected function deleteRelations($relations)
    {
        foreach ($relations as $relation) {
            $relation = $this->$relation;
            if ($relation) {
                /** @var ActiveRecord[] $items */
                $items = is_array($relation) ? $relation : [$relation];
                foreach ($items as $item) {
                    $item->delete();
                }
            }
        }
    }

    /**
     * Возвращает массив сортировок по умолчанию для поиска
     * @return array
     */
    public function searchDefaultOrder()
    {
        $result = [];
        foreach ($this->primaryKey() as $field) {
            $result[$field] = SORT_ASC;
        };
        return $result;
    }

    /**
     * Возврщает источник данных для поиска моделей
     * @param array $params
     * @param ActiveQuery|null $query
     * @return ActiveDataProvider
     * @throws yii\base\InvalidConfigException
     * @throws ReflectionException
     */
    public function search($params, $query = null)
    {
        $query = $query ? $query : static::find();
        $this->load($params);
        $mainAlias = $query->getAlias();
        foreach ($this->attributes() as $attribute) {
            $columnSchema = $this->getTableSchema()->getColumn($attribute);
            if (!$columnSchema) {
                continue;
            }
            $value = $this->getAttribute($attribute);
            $attributeWithAlias = $mainAlias . '.' . $attribute;
            if ($value !== null && $value !== '') {
                switch ($columnSchema->phpType) {
                    case 'string':
                        if ($columnSchema->type == (Yii::$app->db->schema)::TYPE_STRING) {
                            $query->andWhere(['LIKE', 'LOWER(' . $attributeWithAlias . ')', mb_strtolower($value)]);
                        } else if ($columnSchema->type == (Yii::$app->db->schema)::TYPE_UUID) {
                            $query->andWhere(['LIKE', 'LOWER(CAST(' . $attributeWithAlias . ' AS VARCHAR))', mb_strtolower(trim($value))]);
                        } else if (((mb_strpos($columnSchema->type, 'date') !== false) || (mb_strpos($columnSchema->type, 'timestamp') !== false))
                            && is_string($value) && (mb_strpos($value, ' - ') !== false)) {
                            $interval = explode(' - ', $value);
                            if (count($interval) == 2) {
                                $query->andWhere(['BETWEEN', $attributeWithAlias, $interval[0], $interval[1]]);
                            }
                        } else {
                            $query->andWhere([$attributeWithAlias => (string)$value]);
                        }
                        break;
                    case 'integer':
                        $query->andWhere([$attributeWithAlias => (integer)$value]);
                        break;
                    case 'double':
                        $query->andWhere([$attributeWithAlias => (float)$value]);
                        break;
                    case 'boolean':
                        $query->andWhere([$attributeWithAlias => (boolean)$value]);
                        break;
                }
            }
        }
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => $this->searchDefaultOrder()],
        ]);
    }

    /**
     * Признак того, что модель была изменена или только что создана
     * @return boolean
     */
    public function isChanged()
    {
        return (boolean)$this->getDirtyAttributes(null, false);
    }

    /**
     * @return ActiveQuery|object
     * @throws yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::class, [get_called_class()]);
    }

    /**
     * @return string
     */
    static public function getTagForTable()
    {
        return static::class . '#table';
    }

    /**
     * @param integer|string $id
     * @return string
     */
    static public function getTagForRow($id)
    {
        return static::class . '#row#' . $id;
    }

    /**
     * Получение полей, которые нужно заменять на NULL, если они содержат пустые значения
     * @return array
     */
    public function getNullableFields()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        foreach ($this->getNullableFields() as $field) {
            if (!$this->{$field}) {
                $this->{$field} = null;
            }
        }
        return parent::beforeValidate();
    }
}
