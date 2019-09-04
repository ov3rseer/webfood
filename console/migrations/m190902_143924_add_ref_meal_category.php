<?php

use common\components\pgsql\Migration;

class m190902_143924_add_ref_meal_category extends Migration
{
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_meal_category}}', [
            'parent_id' =>$this->integer()->indexed()->foreignKey('{{%ref_meal_category}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\MealCategory']);

        $this->addColumn('{{%ref_meal}}', 'meal_category_id', $this->integer()->indexed()->notNull()->foreignKey('{{%ref_meal_category}}', 'id'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%ref_meal}}', 'meal_category_id');

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\MealCategory']);
        $this->dropTable('{{%ref_meal_category}}');
    }
}