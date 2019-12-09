<?php

use common\components\pgsql\Migration;

class m190710_090741_add_ref_unit extends Migration
{
    public function safeUp()
    {
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
    }

    public function safeDown()
    {
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Unit']);
        $this->dropTable('{{%ref_unit}}');
    }
}