<?php

use common\components\pgsql\Migration;
use yii\base\NotSupportedException;

class m190807_082424_add_ref_meal_category extends Migration
{
    /**
     * @return bool|void
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_meal_category}}');
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\MealCategory']);

        $this->createEnumTable('{{%enum_meal_type}}', [
            1 => 'Прочее',
            2 => 'Блюда для буфета',
            3 => 'Горячее питание',
        ]);
    }

    /**
     * @return bool|void
     */
    public function safeDown()
    {
        $this->dropTable('{{%enum_meal_type}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\MealCategory']);
        $this->dropTable('{{%ref_meal_category}}');
    }
}