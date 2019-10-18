<?php

namespace common\models\form;

use backend\widgets\ActiveField;
use common\models\enum\Enum;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\base\UnknownMethodException;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\helpers\Inflector;
use yii\web\UploadedFile;

/**
 * Базовый класс формы
 */
class Form extends Model
{
    /**
     * Сценарий для поиска моделей в журналах (для совместимости с ActiveForm)
     */
    const SCENARIO_SEARCH = 'search';

    /**
     * @var array массив настроек полей модели
     */
    protected $_fieldsOptions = [];

    /**
     * @var array массив атрибутов с отношениями
     */
    protected $_attributesWithRelations;

    /**
     * @inheritdoc
     */
    public function generateAttributeLabel($name)
    {
        $labels = $this->attributeLabels();
        $newName = Inflector::camel2id($name, '_') . '_id';
        return isset($labels[$newName]) ? $labels[$newName] : parent::generateAttributeLabel($name);
    }

    /**
     * Возвращает массив настроек атрибутов модели
     * @return array
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            $relations = $this->getAttributesWithRelation();
            foreach ($this->attributes() as $attribute) {
                if (isset($relations[$attribute])) {
                    $fieldOptions['type'] =
                        is_subclass_of($relations[$attribute]['class'], Enum::class, true) ?
                            ActiveField::ENUM : ActiveField::REFERENCE;
                } else {
                    $fieldOptions['type'] = ActiveField::STRING;
                }
                $fieldOptions['displayType'] = $fieldOptions['type'];
                $this->_fieldsOptions[$attribute] = $fieldOptions;
            }
        }
        return $this->_fieldsOptions;
    }

    /**
     * Возвращает массив настроек атрибута модели
     * @param string $attribute
     * @return array
     */
    public function getFieldOptions($attribute)
    {
        $fieldsOptions = $this->getFieldsOptions();
        return isset($fieldsOptions[$attribute]) ? $fieldsOptions[$attribute] : [];
    }

    /**
     * Возвращает массив настроек связей атрибутов с другими моделями
     * @return array
     */
    public function getAttributesWithRelation()
    {
        if ($this->_attributesWithRelations === null) {
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
     */
    public function getAttributeRelation($attribute)
    {
        $relations = $this->getAttributesWithRelation();
        return isset($relations[$attribute]) ? $relations[$attribute] : false;
    }

    /**
     * Определение наличия атрибута
     * @param string $attribute
     * @return boolean
     */
    public function hasAttribute($attribute)
    {
        return in_array($attribute, $this->attributes());
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            $fieldsOptions = $this->getFieldsOptions();
            foreach ($fieldsOptions as $field => $fieldOptions) {
                if ($fieldOptions['displayType'] == ActiveField::FILE) {
                    $this->{$field} = UploadedFile::getInstance($this, $field);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            $value = parent::__get($name);
            if ($value instanceof ActiveQueryInterface) {
                return $value->multiple ? $value->all() : $value->one();
            } else {
                return $value;
            }
        }
        return parent::__get($name);
    }

    /**
     * Returns the relation object with the specified name.
     * A relation is defined by a getter method which returns an [[ActiveQueryInterface]] object.
     * It can be declared in either the Active Record class itself or one of its behaviors.
     * @param string $name the relation name, e.g. `orders` for a relation defined via `getOrders()` method (case-sensitive).
     * @param bool $throwException whether to throw exception if the relation does not exist.
     * @return ActiveQueryInterface|ActiveQuery the relational query object. If the relation does not exist
     * and `$throwException` is `false`, `null` will be returned.
     * @throws \ReflectionException
     */
    public function getRelation($name, $throwException = true)
    {
        $getter = 'get' . $name;
        try {
            // the relation could be defined in a behavior
            $relation = $this->$getter();
        } catch (UnknownMethodException $e) {
            if ($throwException) {
                throw new InvalidParamException(get_class($this) . ' has no relation named "' . $name . '".', 0, $e);
            } else {
                return null;
            }
        }
        if (!$relation instanceof ActiveQueryInterface) {
            if ($throwException) {
                throw new InvalidParamException(get_class($this) . ' has no relation named "' . $name . '".');
            } else {
                return null;
            }
        }

        if (method_exists($this, $getter)) {
            // relation name is case sensitive, trying to validate it when the relation is defined within this class
            $method = new \ReflectionMethod($this, $getter);
            $realName = lcfirst(substr($method->getName(), 3));
            if ($realName !== $name) {
                if ($throwException) {
                    throw new InvalidParamException('Relation names are case sensitive. ' . get_class($this) . " has a relation named \"$realName\" instead of \"$name\".");
                } else {
                    return null;
                }
            }
        }

        return $relation;
    }
}
