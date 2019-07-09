<?php

namespace common\models\register\registerAccumulate;

use common\components\DateTime;
use common\models\document\Document;
use common\models\register\Register;
use common\models\system\Entity;
use yii\db\ActiveQuery;

/**
 * Базовая модель регистра накопления
 *
 * Свойства:
 * @property integer $document_basis_type_id
 * @property integer $document_basis_id
 *
 * Отношения:
 * @property Entity $documentBasisType
 */
abstract class RegisterAccumulate extends Register
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['document_basis_type_id', 'document_basis_id'], 'required'],
            [['document_basis_type_id', 'document_basis_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    static public function getDefaultDimensions()
    {
        return array_merge(parent::getDefaultDimensions(), [
            'document_basis_id',
            'document_basis_type_id',
        ]);
    }

    /**
     * Получение списка ресурсов
     * @return array
     */
    static public function getResources()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'document_basis_id'      => 'Документ-основание',
            'document_basis_type_id' => 'Тип документа-основания',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getDocumentBasisType()
    {
        return $this->hasOne(Document::class, ['id' => 'document_basis_type_id']);
    }

    /**
     * Получение команды для запроса итогов по регистру
     * @param DateTime $date дата и время
     * @param array $resources ресурсы, по которым необходимо посчитать сальдо
     * @param array $dimensions измерения, которые будут использоваться в качестве разреза сальдо
     * @param string $tableAlias алиас для таблицы регистра
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    static public function findBalance($date = null, $resources = [], $dimensions = [], $tableAlias = 't')
    {
        if (!$dimensions) {
            $dimensions = static::getDimensions();
        }

        if (!$resources) {
            $resources = static::getResources();
        }

        $groupFields = [];
        foreach ($dimensions as $i => $dimension) {
            if (strpos($dimension, '.') === false && strpos($dimension, '(') === false) {
                $dimensions[$i] = $tableAlias . '.' . $dimension;
            }
            $groupFields[] = preg_replace('/as\s+.*$/i', '', $dimensions[$i]);
        }

        $havingFields = ['AND'];
        foreach ($resources as $i => $resource) {
            if (strpos($resource, '(') === false) {
                $resources[$i] = 'SUM(' . $tableAlias . '.' . $resource . ') AS ' . $resource;
            }
            $havingFields[] = preg_replace('/as\s+.*$/i', '', $resources[$i]) . ' != 0';
        }

        $result = static::find()
            ->select(array_merge($dimensions, $resources))
            ->alias($tableAlias)
            ->groupBy($groupFields)
            ->having($havingFields)
            ->asArray(true);

        if ($date) {
            $result->andWhere($tableAlias . '.date <= :date', [':date' => (string)$date]);
        }

        return $result;
    }
}
