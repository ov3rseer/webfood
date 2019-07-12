<?php

namespace backend\models\report;

use backend\widgets\ActiveField;
use common\models\document\Document;
use common\models\system\Entity;
use yii\data\ArrayDataProvider;

/**
 * Отчет "Связаные документы"
 *
 * Отношения:
 * @property Document $documentBasis
 */
class DocumentDependenceStructure extends Report
{
    /**
     * @var integer
     */
    public $document_basis_id;

    /**
     * @var integer
     */
    public $document_basis_type_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['document_basis_id', 'document_basis_type_id'], 'required'],
            [['document_basis_id', 'document_basis_type_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'document_basis_id' => 'Документ-основание',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['document_basis_id']['displayType'] = ActiveField::HIDDEN;
            $this->_fieldsOptions['document_basis_type_id']['displayType'] = ActiveField::HIDDEN;
        }
        return $this->_fieldsOptions;
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getDocumentBasis()
    {
        if ($this->document_basis_type_id && $this->document_basis_id) {
            /** @var Document $modelClass */
            $modelClass = Entity::getClassNameById($this->document_basis_type_id);
            if ($modelClass) {
                return $modelClass::find()->andWhere(['id' => $this->document_basis_id]);
            }
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        $result = 'Связаные документы';
        if ($this->document_basis_type_id && $this->document_basis_id) {
            /** @var Document $className */
            $className = Entity::getClassNameById($this->document_basis_type_id);
            $model = $className::findOne($this->document_basis_id);
            if ($model) {
                $result .= ': ' . (string)$model;
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function getDataProvider()
    {
        $document = $this->documentBasis;
        $data = $document->getDependenceStructure();
        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getColumns()
    {
        return [];
    }
}
