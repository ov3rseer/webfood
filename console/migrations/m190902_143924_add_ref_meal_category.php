<?php

use common\components\pgsql\Migration;
use yii\base\NotSupportedException;

class m190902_143924_add_ref_meal_category extends Migration
{
    /**
     * @return bool|void
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_meal_category}}');
        $this->addColumn('{{%ref_meal}}', 'meal_category_id',
            $this->integer()->indexed()->notNull()->foreignKey('{{%ref_meal_category}}', 'id')
        );
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\MealCategory']);

        $this->createEnumTable('{{%enum_meal_type}}', [
            1 => 'Прочее',
            2 => 'Блюда для буфета',
            3 => 'Горячее питание',
        ]);
        $this->addColumn('{{%ref_meal}}', 'meal_type_id',
            $this->integer()->indexed()->notNull()->foreignKey('{{%enum_meal_type}}', 'id')
        );
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        $this->dropColumn('{{ref_meal}}', 'meal_type_id');
        $this->dropTable('{{%enum_meal_type}}');

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\MealCategory']);
        $this->dropColumn('{{%ref_meal}}', 'meal_category_id');
        $this->dropTable('{{%ref_meal_category}}');
    }
}