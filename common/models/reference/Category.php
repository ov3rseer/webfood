<?php

namespace common\models\reference;

use yii\base\InvalidConfigException;

/**
 * Базовая модель справочника для категорий
 */
abstract class Category extends Reference
{
    /**
     * Получение дерева категорий
     * @return array
     * @throws InvalidConfigException
     */
    static public function getTree()
    {
        /** @var Category[]|MealCategory[]|ProductCategory[] $rootCategories */
        $rootCategories = self::find()
            ->select(['id', 'name', 'is_active'])
            ->orderBy('name ASC')
            ->all();
        $nodes = [];
        foreach ($rootCategories as $rootCategory) {
            $nodes[] = [
                'id' => $rootCategory->id,
                'name' => $rootCategory->name,
                'data' => [
                    'is_active' => $rootCategory->is_active,
                ],
            ];
        }
        $result[] = [
            'id' => 'root',
            'name' => 'Все рубрики',
            'children' => $nodes,
            'data' => [
                'is_active' => true,
            ],
        ];
        return $result;
    }
}