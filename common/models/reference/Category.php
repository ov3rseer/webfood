<?php

namespace common\models\reference;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * Базовая модель справочника для категорий
 *
 * Свойста
 * @property integer $parent_id
 * @property string  $plural_name
 *
 * Отношения:
 * @property MealCategory|ProductCategory       $parent
 * @property MealCategory[]|ProductCategory[]   $childCategories  прямые дочерние категории
 */
abstract class Category extends Reference
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['parent_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'parent_id'     => 'Родительская категория',
        ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getChildCategories()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id']);
    }

    /**
     * Получение дерева категорий
     * @return array
     * @throws InvalidConfigException
     */
    static public function getTree()
    {
        /** @var Category[]|MealCategory[]|ProductCategory[] $rootCategories */
        $rootCategories = self::find()
            ->select(['id', 'name', 'parent_id', 'is_active'])
            ->andWhere('parent_id IS NULL')
            ->orderBy('name ASC')
            ->all();
        $nodes = [];
        foreach ($rootCategories as $rootCategory) {
            $nodes[] = [
                'id' => $rootCategory->id,
                'name' => $rootCategory->name,
                'children' => self::getTreeBranch($rootCategory),
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

    /**
     * Возвращает данные для указанной категории
     * @param Category $category
     * @return array
     */
    static public function getTreeBranch($category)
    {
        $result = [];
        if ($category->isRelationPopulated('childCategories')) {
            $childCategories = $category->childCategories;
        } else {
            /** @var MealCategory[]|ProductCategory $childCategories */
            $childCategories = $category->getChildCategories()
                ->orderBy('name ASC')
                ->all();
        }
        foreach ($childCategories as $childCategory) {
            $childNode = [
                'id' => $childCategory->id,
                'name' => $childCategory->name,
                'children' => self::getTreeBranch($childCategory),
                'data' => [
                    'is_active' => $childCategory->is_active,
                ],
            ];
            $result[] = $childNode;
        }
        return $result;
    }
}