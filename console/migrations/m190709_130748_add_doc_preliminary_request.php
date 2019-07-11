<?php

use common\components\mysql\Migration;
use yii\rbac\Permission;

class m190709_130748_add_doc_preliminary_request extends Migration
{
    private $_permissionsForAdd = [
        [
            'name' => 'backend\controllers\reference\UnitController.Index',
            'description' => 'Единицы измерения: Журнал',
        ],
        [
            'name' => 'backend\controllers\reference\UnitController.View',
            'description' => 'Единицы измерения: Просмотр',
        ],
        [
            'name' => 'backend\controllers\reference\UnitController.Create',
            'description' => 'Единицы измерения: Создать',
        ],
        [
            'name' => 'backend\controllers\reference\UnitController.Update',
            'description' => 'Единицы измерения: Изменить',
        ],
        [
            'name' => 'backend\controllers\reference\UnitController.Delete',
            'description' => 'Единицы измерения: Удалить',
        ],
        [
            'name' => 'backend\controllers\reference\UnitController.Restore',
            'description' => 'Единицы измерения: Восстановить',
        ],
        [
            'name' => 'backend\controllers\reference\ProductController.Index',
            'description' => 'Продукты: Журнал',
        ],
        [
            'name' => 'backend\controllers\reference\ProductController.View',
            'description' => 'Продукты: Просмотр',
        ],
        [
            'name' => 'backend\controllers\reference\ProductController.Create',
            'description' => 'Продукты: Создать',
        ],
        [
            'name' => 'backend\controllers\reference\ProductController.Update',
            'description' => 'Продукты: Изменить',
        ],
        [
            'name' => 'backend\controllers\reference\ProductController.Delete',
            'description' => 'Продукты: Удалить',
        ],
        [
            'name' => 'backend\controllers\reference\ProductController.Restore',
            'description' => 'Продукты: Восстановить',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.Index',
            'description' => 'Единицы измерения: Журнал',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.View',
            'description' => 'Единицы измерения: Просмотр',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.Create',
            'description' => 'Единицы измерения: Создать',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.Update',
            'description' => 'Единицы измерения: Изменить',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.Delete',
            'description' => 'Единицы измерения: Удалить',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.Restore',
            'description' => 'Единицы измерения: Восстановить',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Index',
            'description' => 'Продукты: Журнал',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.View',
            'description' => 'Продукты: Просмотр',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Create',
            'description' => 'Продукты: Создать',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Update',
            'description' => 'Продукты: Изменить',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Delete',
            'description' => 'Продукты: Удалить',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Restore',
            'description' => 'Продукты: Восстановить',
        ],
    ];

    /**
     * @return bool|void
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createEnumTable('{{%enum_type_request}}', [
            1 => 'Дети',
            2 => 'Сотрудники'
        ]);

        $this->createReferenceTable('{{%ref_unit}}', [
            'name_full' => $this->string()->notNull()->indexed(),
            'code' => $this->string(3)->notNull()->indexed()->unsigned(),
            'international_abbreviation' => $this->string(3)->notNull()->indexed(),
        ]);

        $units = [
            ['мм',   'миллиметр',        '003', 'MMT'],
            ['см',   'сантиметр',        '004', 'CMT'],
            ['м',    'метр',             '006', 'MTR'],

            ['г',    'грамм',            '163', 'GRM'],
            ['кг',   'килограмм',        '166', 'KGM'],
            ['т',    'тонна',            '168', 'TNE'],

            ['л',    'литр',             '112', 'LTR'],
            ['м3',   'кубический метр',  '113', 'MTQ'],

            ['боб',  'бобина (бухта)',   '616', 'NBB'],
            ['шт',   'штука',            '796', 'PCE'],
            ['упак', 'упаковка',         '778', 'NMP'],
        ];

        $this->batchInsert('{{%ref_unit}}', ['name', 'name_full', 'code', 'international_abbreviation'], $units);

        $this->createReferenceTable('{{%ref_product}}', [
            'product_code' => $this->integer(9)->notNull()->unsigned(),
            'unit_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_unit}}', 'id'),
        ]);

        $this->createDocumentTable('{{%doc_preliminary_request}}', [
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'type_request_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_type_request}}', 'id'),
            'quantity' => $this->float()
        ]);

        $this->createDocumentTable('{{%doc_correction_request}}');

        $auth = Yii::$app->authManager;
        foreach ($this->_permissionsForAdd as $permissionData) {
            $permission = new Permission($permissionData);
            $auth->add($permission);
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        foreach ($this->_permissionsForRemove as $permissionData) {
            $permission = $auth->getPermission($permissionData['name']);
            if ($permission) {
                $auth->remove($permission);
            }
        }

        $this->dropTable('{{%doc_correction_request}}');
        $this->dropTable('{{%doc_preliminary_request}}');
        $this->dropTable('{{%ref_product}}');
        $this->dropTable('{{%ref_unit}}');
        $this->dropTable('{{%enum_type_request}}');
    }
}