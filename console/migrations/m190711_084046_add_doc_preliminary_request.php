<?php

use common\components\pgsql\Migration;
use yii\base\NotSupportedException;

class m190711_084046_add_doc_preliminary_request extends Migration
{
    /**
     * @return bool|void
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createEnumTable('{{%enum_contract_type}}', [
            1 => 'Дети',
            2 => 'Сотрудники'
        ]);

        $this->createReferenceTable('{{%ref_unit}}', [
            'code' => $this->string(3)->notNull()->indexed()->unsigned(),
            'international_abbreviation' => $this->string(3)->notNull()->indexed(),
        ]);

        $table = Yii::$app->db->schema->getTableSchema('{{%ref_unit}}');
        if (!isset($table->columns['name_full'])) {
            $this->addColumn('{{%ref_unit}}', 'name_full', $this->string(1024));
        }

        $units = [
            ['г',    'грамм',            '163', 'GRM'],
            ['кг',   'килограмм',        '166', 'KGM'],

            ['л',    'литр',             '112', 'LTR'],
            ['м3',   'кубический метр',  '113', 'MTQ'],

            ['шт',   'штука',            '796', 'PCE'],
            ['упак', 'упаковка',         '778', 'NMP'],
        ];
        $this->batchInsert('{{%ref_unit}}', ['name', 'name_full', 'code', 'international_abbreviation'], $units);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Unit']);

        $this->createReferenceTable('{{%ref_product}}', [
            'product_code' => $this->string(9)->notNull()->unique(),
            'price' => $this->decimal(10, 2)->notNull(),
            'unit_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_unit}}', 'id'),
        ]);
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\reference\Product']);

        $this->createDocumentTable('{{%doc_request}}');

        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\Request']);

        $this->createTablePartTable('{{%tab_request_date}}', '{{%doc_request}}', [
            'week_day_date' => $this->date()->notNull(),
        ]);

        $this->createCrossTable('{{%cross_request_date_product}}', [
            'request_date_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%tab_request_date}}', 'id'),
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'unit_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'planned_quantity' => $this->float()->notNull(),
            'current_quantity' => $this->float()->notNull(),
        ]);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->dropTable('{{%cross_request_date_product}}');
        $this->dropTable('{{%tab_request_date}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\Request']);
        $this->dropTable('{{%doc_request}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Product']);
        $this->dropTable('{{%ref_product}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Unit']);
        $this->dropTable('{{%ref_unit}}');
        $this->dropTable('{{%enum_contract_type}}');
    }
}