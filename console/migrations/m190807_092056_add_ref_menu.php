<?php

use common\components\pgsql\Migration;

class m190807_092056_add_ref_menu extends Migration
{
    /**
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createReferenceTable('{{%ref_menu}}');
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Menu']);

        $this->createTablePartTable('{{%tab_menu_meal}}', '{{%ref_menu}}', [
            'meal_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_meal}}', 'id'),
            'quantity' => $this->integer()->notNull(),
        ]);
        $this->createTablePartTable('{{%tab_menu_complex}}', '{{%ref_menu}}', [
            'complex_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_complex}}', 'id'),
            'quantity' => $this->integer()->notNull(),
        ]);

        $this->createEnumTable('{{%enum_menu_cycle}}', [
            1 => 'Еженедельно',
            2 => 'Нечётные недели',
            3 => 'Чётные недели'
        ]);

        $this->createEnumTable('{{%enum_week_day}}', [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ]);

        $this->createReferenceTable('{{%ref_set_menu}}', [
            'menu_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_menu}}', 'id'),
            'menu_cycle_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_menu_cycle}}', 'id'),
            'week_day_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_week_day}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\SetMenu']);

        $this->createEnumTable('{{enum_day_type}}',[
           1 => 'Выходной день',
        ]);

        $this->createRegisterTable('{{%reg_weekend}}', [
            'date'          => $this->date()->notNull(),
            'day_type_id'   => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_day_type}}', 'id'),
        ]);
    }

    /**
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%reg_weekend}}');
        $this->dropTable('{{%enum_day_type}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\SetMenu']);
        $this->dropTable('{{%ref_set_menu}}');
        $this->dropTable('{{%enum_week_day}}');
        $this->dropTable('{{%enum_menu_cycle}}');
        $this->dropTable('{{%tab_menu_complex}}');
        $this->dropTable('{{%tab_menu_meal}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Menu']);
        $this->dropTable('{{%ref_menu}}');
    }
}