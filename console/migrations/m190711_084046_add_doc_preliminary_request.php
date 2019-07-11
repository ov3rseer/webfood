<?php

use common\components\mysql\Migration;
use yii\rbac\Permission;

class m190711_084046_add_doc_preliminary_request extends Migration
{
    private $_permissionsForUnit = [
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
        ]
    ];

    private $_permissionsForProduct = [
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
        ]
    ];

    private $_permissionsForPreliminaryRequest = [[
        'name' => 'backend\controllers\document\PreliminaryRequestController.Index',
        'description' => 'Предварительные заявки: Журнал',
    ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.View',
            'description' => 'Предварительные заявки: Просмотр',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.Create',
            'description' => 'Предварительные заявки: Создать',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.Update',
            'description' => 'Предварительные заявки: Изменить',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.Delete',
            'description' => 'Предварительные заявки: Удалить',
        ],
        [
            'name' => 'backend\controllers\document\PreliminaryRequestController.Restore',
            'description' => 'Предварительные заявки: Восстановить',
        ]
    ];

    private $_permissionsForCorrectionRequest = [
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Index',
            'description' => 'Корректировки заявок: Журнал',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.View',
            'description' => 'Корректировки заявок: Просмотр',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Create',
            'description' => 'Корректировки заявок: Создать',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Update',
            'description' => 'Корректировки заявок: Изменить',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Delete',
            'description' => 'Корректировки заявок: Удалить',
        ],
        [
            'name' => 'backend\controllers\document\CorrectionRequestController.Restore',
            'description' => 'Корректировки заявок: Восстановить',
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
        ]);

        $this->createDocumentTable('{{%doc_preliminary_request}}', [
            'type_request_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%enum_type_request}}', 'id'),
        ]);

        $this->createTablePartTable('{{%tab_preliminary_request_product}}', '{{%doc_preliminary_request}}', [
            'product_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_product}}', 'id'),
            'unit_id' => $this->integer()->notNull()->indexed()->foreignKey('{{%ref_unit}}', 'id'),
            'quantity' => $this->decimal(10, 2)->notNull(),
        ]);

        $this->createDocumentTable('{{%doc_correction_request}}');

        $permissionForAdd = array_merge(
            $this->_permissionsForUnit,
            $this->_permissionsForProduct,
            $this->_permissionsForPreliminaryRequest,
            $this->_permissionsForCorrectionRequest
        );
        $auth = Yii::$app->authManager;
        foreach ($permissionForAdd as $permissionData) {
            $permission = new Permission($permissionData);
            $auth->add($permission);
        }
    }

    public function safeDown()
    {
        $permissionForDelete = array_merge(
            $this->_permissionsForUnit,
            $this->_permissionsForProduct,
            $this->_permissionsForPreliminaryRequest,
            $this->_permissionsForCorrectionRequest
        );
        $auth = Yii::$app->authManager;
        foreach ($permissionForDelete as $permissionData) {
            $permission = $auth->getPermission($permissionData['name']);
            if ($permission) {
                $auth->remove($permission);
            }
        }

        $this->dropTable('{{%doc_correction_request}}');
        $this->dropTable('{{%tab_preliminary_request_product}}');
        $this->dropTable('{{%doc_preliminary_request}}');
        $this->dropTable('{{%ref_product}}');
        $this->dropTable('{{%ref_unit}}');
        $this->dropTable('{{%enum_type_request}}');
    }
}