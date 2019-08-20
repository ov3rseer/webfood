<?php

namespace common\models\system;

use common\models\reference\Product;
use yii\db\Query;

class Structure extends System
{
    private $dataBaseStructure;

    /*public function __construct($config = []) {
        parent::__construct($config);
        $structure = $this->getStructure();
        if ($structure) {
            $this->dataBaseStructure = json_decode($structure->structure, $assoc = true);
        }
    }*/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['structure'], 'string'],
        ]);
    }

    /*public function getStructure() {
        $query = new Query();
        $structure = $query->select('*')->from($this::tableName())->where(['id' => 1])->one();
        return $structure;
    }

    public function createTable($tableName) {
        if (!isset($this->dataBaseStructure[$tableName])) {
            $this->dataBaseStructure[$tableName] = [];
        }
    }

    public function saveStructure() {
        $structure = $this->getStructure();
        if ($structure) {
            $structure->structure = json_encode($this->dataBaseStructure);
            $structure->save();
        }
    }*/

    public function createTable($structure, $tableName) {
        if (!isset($structure[$tableName])) {
            $structure[$tableName] = [];
        }
        return $structure;
    }

    public function addColumn($structure, $tableName, $columnName, $foreignTableName = null, $foreignColumnName = null) {
        if (!isset($structure[$tableName][$columnName])) {
            $structure[$tableName][$columnName] = [
                'linkedTo' => [],
                'linkedFrom' => [],
            ];
            /*if ($foreignTableName && $foreignColumnName) {
                if (!isset($structure[$tableName]['linkedTo'][$foreignTableName])) {
                    $structure[$tableName]['linkedTo'][$foreignTableName] = [];
                }
                if (!isset($structure[$tableName]['linkedTo'][$foreignTableName][$columnName])) {
                    $structure[$tableName]['linkedTo'][$foreignTableName][$columnName] = [];
                }
                $structure[$tableName]['linkedTo'][$foreignTableName][$columnName][] = $foreignColumnName;

                if (isset($structure[$foreignTableName])) {
                    if (!isset($structure[$foreignTableName]['linkedFrom'][$tableName])) {
                        $structure[$foreignTableName]['linkedFrom'][$tableName] = [];
                    }
                    if (!isset($structure[$foreignTableName]['linkedFrom'][$tableName][$foreignColumnName])) {
                        $structure[$foreignTableName]['linkedFrom'][$tableName][$foreignColumnName] = [];
                    }
                    $structure[$foreignTableName]['linkedFrom'][$tableName][$foreignColumnName][] = $columnName;
                }
            } else {
                $structure[$tableName]['free'][] = $columnName;
            }*/
        }
        if ($foreignTableName && $foreignColumnName) {
            $structure[$tableName][$columnName]['linkedTo'][$foreignTableName] = $foreignColumnName;

            if (!isset($structure[$foreignTableName][$foreignColumnName])) {
                $structure[$foreignTableName][$foreignColumnName] = [
                    'linkedTo' => [],
                    'linkedFrom' => [],
                ];
            }

            $structure[$foreignTableName][$foreignColumnName]['linkedFrom'][$tableName] = $columnName;
        }
        return $structure;
    }

    public function getStructureByResult($result) {
        if ($result) {
            return json_decode($result->structure, $assoc = true);
        }
        return 0;
    }
}