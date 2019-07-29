<?php

use common\components\pgsql\Migration;
use yii\base\NotSupportedException;

class m190711_084046_add_doc_preliminary_request extends Migration
{
    private $_permissionsForUnit;
    private $_permissionsForProduct;
    private $_permissionsForRequest;

    /**
     * @throws Exception
     */
    public function setPermissions()
    {
        $this->_permissionsForUnit = $this->getPermissions('backend\controllers\reference\UnitController', 'Единицы измерения', 63);
        $this->_permissionsForProduct = $this->getPermissions('backend\controllers\reference\ProductController', 'Продукты', 63);
        $this->_permissionsForRequest = $this->getPermissions('backend\controllers\document\RequestController', 'Заявки', 63);
    }

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

        $this->createDocumentTable('{{%doc_correction_request}}');
        $this->insert('{{%sys_entity}}', ['class_name' => 'common\models\document\CorrectionRequest']);

        $this->setPermissions();
        $permissionForAdd = array_merge(
            $this->_permissionsForUnit,
            $this->_permissionsForProduct,
            $this->_permissionsForRequest
        );
        $this->addPermissions($permissionForAdd);
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function safeDown()
    {
        $this->setPermissions();
        $permissionForDelete = array_merge(
            $this->_permissionsForUnit,
            $this->_permissionsForProduct,
            $this->_permissionsForPreliminaryRequest,
            $this->_permissionsForCorrectionRequest
        );
        $this->deletePermissions($permissionForDelete);

        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\CorrectionRequest']);
        $this->dropTable('{{%doc_correction_request}}');
        $this->dropTable('{{%tab_preliminary_request_product}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\document\PreliminaryRequest']);
        $this->dropTable('{{%doc_preliminary_request}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Product']);
        $this->dropTable('{{%ref_product}}');
        $this->delete('{{%sys_entity}}', ['class_name' => 'common\models\reference\Unit']);
        $this->dropTable('{{%ref_unit}}');
        $this->dropTable('{{%enum_contract_type}}');
    }
}