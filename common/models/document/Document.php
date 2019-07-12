<?php

namespace common\models\document;

use backend\widgets\ActiveField;
use common\components\DateTime;
use common\exceptions\RegisterException;
use common\models\ActiveRecord;
use common\models\enum\DocumentStatus;
use common\models\reference\User;
use common\models\register\registerAccumulate\RegisterAccumulate;
use common\models\system\Entity;
use common\queries\DocumentQuery;
use Throwable;
use yii;
use yii\base\Exception;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * Базовая модель документа
 *
 * @property DateTime $date
 * @property integer  $status_id
 * @property integer  $create_user_id
 * @property integer  $update_user_id
 * @property DateTime $create_date
 * @property DateTime $update_date
 * @property integer  $document_basis_id
 * @property integer  $document_basis_type_id
 *
 * Отношения:
 * @property DocumentStatus $status
 * @property User $createUser
 * @property User $updateUser
 * @property Document $documentBasis
 */
abstract class Document extends ActiveRecord
{
    /**
     * @var string префикс таблицы
     */
    protected static $tablePrefix = 'doc_';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['date', 'status_id'], 'required'],
            [['date'], 'date', 'format' => 'php:' . DateTime::DB_DATETIME_FORMAT],
            [['status_id', 'document_basis_id', 'document_basis_type_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     * @throws Throwable
     */
    public function behaviors()
    {
        $result = [];
        if (User::isBackendUser()) {
            $result['blameable'] = [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => $this->hasAttribute('create_user_id') ? 'create_user_id' : false,
                'updatedByAttribute' => $this->hasAttribute('update_user_id') ? 'update_user_id' : false,
                'value' => Yii::$app->user->id,
            ];
        }
        $result['create_timestamp'] = [
            'class'              => TimestampBehavior::class,
            'createdAtAttribute' => $this->hasAttribute('create_date') ? 'create_date' : false,
            'updatedAtAttribute' => false,
            'value'              => function() {
                return $this->create_date ?: new yii\db\Expression('NOW()');
            },
        ];
        $result['update_timestamp'] = [
            'class'              => TimestampBehavior::class,
            'createdAtAttribute' => false,
            'updatedAtAttribute' => $this->hasAttribute('update_date') ? 'update_date' : false,
            'skipUpdateOnClean'  => false,
            'value'              => function() {
                return $this->scenario == static::SCENARIO_SYSTEM ? $this->update_date : new yii\db\Expression('NOW()');
            },
        ];
        return array_merge(parent::behaviors(), $result);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'id'                => 'Номер',
            'date'              => 'Дата',
            'status_id'         => 'Статус',
            'create_date'       => 'Дата создания',
            'update_date'       => 'Дата изменения',
            'create_user_id'    => 'Автор',
            'update_user_id'    => 'Автор последнего изменения',
            'document_basis_id' => 'Документ-основание',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $parentResult = parent::beforeValidate();
        if ($parentResult && $this->isNewRecord) {
            if (!$this->date) {
                $this->date = new DateTime();
            }
            if (!$this->status_id) {
                $this->status_id = DocumentStatus::DRAFT;
            }
        }
        return $parentResult;
    }

    /**
     * Магическая функция приведения объекта к строке
     * @return string
     */
    public function __toString()
    {
        return $this->getSingularName() . ' ' . $this->getNumberAndDate();
    }

    /**
     * Получение номера и даты документа
     * @return string
     */
    public function getNumberAndDate()
    {
        return ($this->isNewRecord ? '(новый)' : '№' .$this->id . ' от ' . $this->date->format('d.m.Y'));
    }

    /**
     * @return DocumentQuery|object
     * @throws yii\base\InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(DocumentQuery::class, [get_called_class()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(DocumentStatus::class, ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUser()
    {
        return $this->hasOne(User::class, ['id' => 'create_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdateUser()
    {
        return $this->hasOne(User::class, ['id' => 'update_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentBasis()
    {
        if ($this->document_basis_type_id && $this->document_basis_id) {
            $modelClass = Entity::getClassNameById($this->document_basis_type_id);
            if ($modelClass) {
                return $this->hasOne($modelClass, ['id' => 'document_basis_id']);
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['create_user_id']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['update_user_id']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['date']['displayType'] = ActiveField::DATETIME;
            $this->_fieldsOptions['create_date']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['update_date']['displayType'] = ActiveField::READONLY;
            $this->_fieldsOptions['document_basis_type_id']['displayType'] = ActiveField::HIDDEN;
            $this->_fieldsOptions['document_basis_id']['displayType'] = ActiveField::READONLY;
        }
        return $this->_fieldsOptions;
    }

    /**
     * @inheritdoc
     * @param bool $runValidation
     * @param null $attributes
     * @param bool $onlyIfChanged
     * @return bool
     * @throws yii\base\UserException
     */
    public function save($runValidation = true, $attributes = null, $onlyIfChanged = false)
    {
        return parent::save($runValidation, $attributes, $onlyIfChanged);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        // Контроль проведения
        $oldStatusId = !empty($changedAttributes['status_id']) ? $changedAttributes['status_id'] : $this->status_id;
        $newStatusId = $this->status_id;
        $this->checkRegisters($oldStatusId, $newStatusId);

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Получение настроек для зависимых регистров.
     * Для каждого регистра указывается функция для формирования записей по документу.
     * @return array
     *     'КлассРегистра' => function (Document $documentModel) { return []; },
     */
    public function getSettingsForDependentRegisters()
    {
        return [];
    }

    /**
     * Контроль остатков по регистрам
     * @param integer $oldStatusId
     * @param integer $newStatusId
     * @throws RegisterException
     * @throws Exception
     */
    protected function checkRegisters($oldStatusId, $newStatusId)
    {
        $dependentRegisters = array_keys($this->getSettingsForDependentRegisters());

        // Очистка регистров

        $oldRegistersRecords = [];
        if ($oldStatusId == DocumentStatus::POSTED) {
            foreach ($dependentRegisters as $registerClassName) {
                $oldRegistersRecords[$registerClassName] = $this->clearDependentRegister($registerClassName);
            }
        }

        // Заполнение регистров

        $newRegistersRecords = [];
        if ($newStatusId == DocumentStatus::POSTED) {
            foreach ($dependentRegisters as $registerClassName) {
                $newRegistersRecords[$registerClassName] = $this->fillDependentRegister($registerClassName);
            }
        }

        // Контроль остатков

        if ($oldStatusId == DocumentStatus::POSTED || $newStatusId == DocumentStatus::POSTED) {
            $registersErrors = [];
            foreach ($dependentRegisters as $registerClassName) {
                if (is_subclass_of($registerClassName, RegisterAccumulate::className(), true)) {
                    /** @var RegisterAccumulate|string $registerClassName */
                    $dimensions = $registerClassName::getSpecialDimensions();
                    $dimensionsSet = [];
                    $oldRegisterRecords = isset($oldRegistersRecords[$registerClassName])
                        ? $oldRegistersRecords[$registerClassName] : [];
                    foreach ($oldRegisterRecords as $oldRegisterRecord) {
                        $dimensionSet = [];
                        foreach ($dimensions as $dimension) {
                            $dimensionSet[$dimension] = $oldRegisterRecord[$dimension];
                        }
                        $dimensionsSet[] = $dimensionSet;
                    }
                    $newRegisterRecords = isset($newRegistersRecords[$registerClassName])
                        ? $newRegistersRecords[$registerClassName] : [];
                    foreach ($newRegisterRecords as $newRegisterRecord) {
                        $dimensionSet = [];
                        foreach ($dimensions as $dimension) {
                            $dimensionSet[$dimension] = $newRegisterRecord[$dimension];
                        }
                        $dimensionsSet[] = $dimensionSet;
                    }
                    $dimensionsSet = array_unique($dimensionsSet, SORT_REGULAR);
                    $registerErrors = $this->checkDependentRegister($registerClassName, $dimensionsSet);
                    if ($registerErrors) {
                        $registersErrors[$registerClassName] = $registerErrors;
                    }
                }
            }
            if ($registersErrors) {
                throw new RegisterException($this, $registersErrors);
            }
        }
    }

    /**
     * Очистка записей в зависимом регистре
     * @param RegisterAccumulate|string $registerClassName - имя класса регистра
     * @throws Exception
     * @return array массив данных, удаленных из регистра
     */
    protected function clearDependentRegister($registerClassName)
    {
        if (!is_subclass_of($registerClassName, RegisterAccumulate::className(), true)) {
            throw new Exception('Document::clearDependentRegister() failed for register: "'. $registerClassName . '"');
        }

        // Метод deleteAll() использовать нельзя,
        // так как в нем не генерируются события "onBeforeDelete" и "onAfterDelete"
        $records = $registerClassName::findAll([
            'document_basis_id' => $this->id,
            'document_basis_type_id' => Entity::getIdByClassName(static::className()),
        ]);
        foreach ($records as $record) {
            $record->delete();
        }

        return $records;
    }

    /**
     * Заполнение зависимого регистра
     * @param RegisterAccumulate|string $registerClassName - имя класса регистра
     * @throws Exception
     * @return array массив данных, добавленных в регистр
     */
    protected function fillDependentRegister($registerClassName)
    {
        $settingsForDependentRegisters = $this->getSettingsForDependentRegisters();

        if (!is_subclass_of($registerClassName, RegisterAccumulate::className(), true) ||
            !isset($settingsForDependentRegisters[$registerClassName]['function']) ||
            !is_callable($settingsForDependentRegisters[$registerClassName]['function'])) {
            throw new Exception('Document::fillDependentRegister() failed for register: "'. $registerClassName . '"');
        }

        /** @var array $registerRows */
        $registerRows = call_user_func($settingsForDependentRegisters[$registerClassName]['function'], $this);
        $registerModels = [];
        foreach ($registerRows as $registerRow) {
            $registerRow['document_basis_id'] = $this->id;
            $registerRow['document_basis_type_id'] = Entity::getIdByClassName(static::className());
            $registerRow['date'] = (string)$this->date;
            /** @var RegisterAccumulate $registerModel */
            $registerModel = new $registerClassName();
            $registerModel->setAttributes($registerRow);
            $registerModel->save();
            $registerModels[] = $registerModel;
        }

        return $registerModels;
    }

    /**
     * Контроль остатков для зависимого регистра
     * @param RegisterAccumulate|string $registerClassName - имя класса регистра
     * @param array  $dimensionsSet - массив наборов измерений для проверки
     * @param bool   $checkForPositiveRest - проверка положительности остатка (если false, то проверка отрицательности остатка)
     * @throws Exception
     * @return array
     */
    protected function checkDependentRegister($registerClassName, $dimensionsSet, $checkForPositiveRest = true)
    {
        if (!is_subclass_of($registerClassName, RegisterAccumulate::className(), true)) {
            throw new Exception('Document::checkDependentRegister() failed for register: "'. $registerClassName . '"');
        }

        $result = [];

        $resources = $registerClassName::getResources();
        $balanceRows = $this->getDependentRegisterBalance($registerClassName, $dimensionsSet);
        if ($balanceRows) {
            foreach ($balanceRows as $balanceRow) {
                foreach ($resources as $resource) {
                    if (($checkForPositiveRest ? 1 : -1) * $balanceRow[$resource] < 0) {
                        $result[] = $balanceRow;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Функция получения остатков в заданном регистре по измерениям, используемым в текущему документе
     * @param RegisterAccumulate|string $registerClassName - имя класса регистра
     * @param array $dimensionsSet - массив наборов измерений регистра
     * @return array - массив сгруппированных по измерениям строк с остатками
     * @throws Exception
     */
    protected function getDependentRegisterBalance($registerClassName, $dimensionsSet)
    {
        if (!is_subclass_of($registerClassName, RegisterAccumulate::className(), true)) {
            throw new Exception('Document::getDependentRegisterBalance() failed for register: "'. $registerClassName . '"');
        }

        if (!$dimensionsSet) {
            return [];
        }

        array_unshift($dimensionsSet, 'OR');

        $balanceQuery = $registerClassName::findBalance(null, $registerClassName::getResources(), $registerClassName::getSpecialDimensions());
        $balanceQuery->andWhere($dimensionsSet);
        return $balanceQuery->all();
    }

    /**
     * Метод для получения структуры подчиненности текущего документа
     * @return array массив со структурой подчиненности документа
     * @throws yii\base\InvalidConfigException
     */
    public function getDependenceStructure()
    {
        /** @var Document $rootDocumentBasis */
        $rootDocumentBasis = $this->findRootDocumentBasis();
        $typeId = Entity::getIdByClassName($rootDocumentBasis::className());
        $allDocumentsIds = [$typeId . '_' . $rootDocumentBasis->id];
        $result = [
            [
                'id'       => $typeId . '_' . $rootDocumentBasis->id,
                'name'     => (string)$rootDocumentBasis,
                'children' => $rootDocumentBasis->getDependentChilds($allDocumentsIds),
                'data' => [
                    'id'        => $rootDocumentBasis->id,
                    'type_id'   => $typeId,
                    'url'       => $rootDocumentBasis->getUpdateUrl(),
                    'status_id' => $rootDocumentBasis->status_id,
                ],
            ],
        ];
        return $result;
    }

    /**
     * @param array $allDocumentsIds
     * @return array массив подчиненых документов текущему документу
     * @throws yii\base\InvalidConfigException
     */
    public function getDependentChilds(&$allDocumentsIds)
    {
        $result = [];
        $documentClasses = Entity::find()
            ->select(['class_name'])
            ->andWhere(['LIKE', 'class_name', '\\document\\'])
            ->asArray()
            ->column();
        foreach ($documentClasses as $documentClass) {
            /** @var Document $documentClass */
            $documents = $documentClass::findAll([
                'document_basis_type_id' => Entity::getIdByClassName($this::className()),
                'document_basis_id' => $this->id,
            ]);
            foreach ($documents as $document) {
                $typeId = Entity::getIdByClassName($document::className());
                if (in_array($typeId . '_' . $document->id, $allDocumentsIds)) {
                    continue;
                }
                $allDocumentsIds[] = $typeId . '_' . $document->id;
                $result[] = [
                    'id'       => $typeId . '_' . $document->id,
                    'name'     => (string)$document,
                    'children' => $document->getDependentChilds($allDocumentsIds),
                    'data'     => [
                        'id'        => $document->id,
                        'type_id'   => $typeId,
                        'url'       => $document->getUpdateUrl(),
                        'status_id' => $document->status_id,
                    ],
                ];
            }
        }
        return $result;
    }

    /**
     * Метод для поиска корневого документа
     */
    public function findRootDocumentBasis()
    {
        return $this->document_basis_id && $this->document_basis_type_id
            ? $this->documentBasis->findRootDocumentBasis()
            : $this;
    }

    /**
     * Получение настроек для создания связанных документов
     * Для каждого типа документа указывается функция для его заполнения
     * @return array
     *     'КлассДокумента' => function(Document $sourceDocument, Document $targetDocument) {}
     */
    public function getSettingsForRelatedDocuments()
    {
        return [];
    }

    /**
     * Создание связанного документа на основании текущего документа
     * @param string $relatedDocumentClassName - имя класса модели связанного документа
     * @throws Exception
     * @return Document новый документ
     */
    public function createRelated($relatedDocumentClassName)
    {
        $settingsForRelatedDocuments = $this->getSettingsForRelatedDocuments();
        if (!isset($settingsForRelatedDocuments[$relatedDocumentClassName]) || !is_callable($settingsForRelatedDocuments[$relatedDocumentClassName])) {
            throw new Exception('Document::createRelated() failed for document class: "'. $relatedDocumentClassName . '"');
        }
        /** @var Document $targetDocument */
        $targetDocument = new $relatedDocumentClassName();
        $targetDocument->document_basis_id = $this->id;
        $targetDocument->document_basis_type_id = Entity::getIdByClassName(static::class);
        call_user_func($settingsForRelatedDocuments[$relatedDocumentClassName], $this, $targetDocument);
        return $targetDocument;
    }

    /**
     * Перезаполнение связанного документа на основании текущего документа
     * @param Document $targetDocument - существующая модель связанного документа
     * @throws Exception
     * @return Document существующий документ
     */
    public function refillRelated($targetDocument)
    {
        $relatedDocumentClassName = $targetDocument::className();
        $settingsForRelatedDocuments = $this->getSettingsForRelatedDocuments();
        if (!isset($settingsForRelatedDocuments[$relatedDocumentClassName]) || !is_callable($settingsForRelatedDocuments[$relatedDocumentClassName])) {
            throw new Exception('Document::refillRelated() failed for document class: "'. $relatedDocumentClassName . '"');
        }
        if ($targetDocument->document_basis_id != $this->id ||
            $targetDocument->document_basis_type_id != Entity::getIdByClassName(static::class)) {
            throw new Exception('Document::refillRelated() wrong document basis');
        }
        call_user_func($settingsForRelatedDocuments[$relatedDocumentClassName], $this, $targetDocument);
        return $targetDocument;
    }

    /**
     * Получение суммы документа
     * @return float
     */
    public function getDocumentSum()
    {
        return 0;
    }

    /**
     * Получение URL для создания нового документа
     * @param array $params
     * @return string
     */
    static public function getCreateUrl($params = [])
    {
        $path = explode('\\', static::class);
        $shortClassName = array_pop($path);
        return Url::to(array_merge(
            ['/document/' . Inflector::camel2id($shortClassName, '-') . '/create'],
            $params
        ));
    }

    /**
     * Получение URL для редактирования существующего документа
     * @param array $params
     * @return string
     */
    public function getUpdateUrl($params = [])
    {
        $path = explode('\\', static::class);
        $shortClassName = array_pop($path);
        return Url::to(array_merge(
            ['/document/' . Inflector::camel2id($shortClassName, '-') . '/update', 'id' => $this->id],
            $params
        ));
    }

    /**
     * @inheritdoc
     */
    public function searchDefaultOrder()
    {
        return [
            'id' => SORT_DESC,
        ];
    }
}
