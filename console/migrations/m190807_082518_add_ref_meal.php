<?php

use common\components\pgsql\Migration;

class m190807_082518_add_ref_meal extends Migration
{
    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_meal}}', [
            'meal_category_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%ref_meal_category}}', 'id'),
            'food_type_id' => $this->integer()->indexed()->notNull()->foreignKey('{{%enum_food_type}}', 'id'),
            'meal_output' => $this->string(30),
            'price' => $this->decimal(10, 2)->notNull(),
            'description' => $this->text(),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Meal']);

        $this->createTablePartTable('{{%tab_meal_product}}', '{{%ref_meal}}', [
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'unit_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_unit}}', 'id'),
            'product_quantity' => $this->decimal(10, 2)->notNull(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%tab_meal_product}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Meal']);
        $this->dropTable('{{%ref_meal}}');
    }
}