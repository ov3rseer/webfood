<?php

namespace common\models\system;

use backend\controllers\document\DocumentController;
use backend\controllers\reference\ReferenceController;
use backend\widgets\ActiveField;
use common\components\DbManager;
use common\models\ActiveRecord;
use common\queries\RoleQuery;
use Yii;
use yii\base\UserException;
use yii\data\ArrayDataProvider;
use yii\db\Query;

/**
 * Модель роли RBAC
 *
 * @property string $name
 * @property string $description
 */
class Role extends ActiveRecord
{
    /**
     * @var array
     */
    public $assigned = [];

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $result = parent::scenarios();
        $result[self::SCENARIO_SEARCH] = $result[self::SCENARIO_DEFAULT];
        $result[self::SCENARIO_SEARCH][] = 'name';
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['description'], 'string', 'max' => 255],
            [['assigned'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name'        => 'ID',
            'description' => 'Наименование',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sys_auth_item}}';
    }

    /**
     * @inheritdoc
     */
    static public function find($modelClass = false)
    {
        return Yii::createObject(RoleQuery::className(), [$modelClass ? $modelClass : get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'Роль';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'Роли';
    }

    /**
     * @inheritdoc
     */
    public function getFieldsOptions()
    {
        if ($this->_fieldsOptions === []) {
            parent::getFieldsOptions();
            $this->_fieldsOptions['description']['displayType'] = ActiveField::STRING;
            $this->_fieldsOptions['assigned']['displayType'] = ActiveField::IGNORE;
        }
        return $this->_fieldsOptions;
    }

    public function getPermissionDataProviders()
    {
        $data = [
            'reference' => [],
            'document'  => [],
            'other'     => [],
        ];
        $assignedPermissions = [];
        if (!$this->isNewRecord) {
            $assignedPermissions = Yii::$app->authManager->getPermissionsByRole($this->name);
        }
        foreach (Yii::$app->authManager->getPermissions() as $permission) {
            $isAssigned = array_key_exists($permission->name, $assignedPermissions);
            $permissionDetails = explode('.', $permission->name);
            $permissionClass = $permissionDetails[0];
            $permissionAction = $permissionDetails[1];
            $permissionDetails = explode(': ', $permission->description);
            $permissionDescriptionClass = $permissionDetails[0];
            if (is_subclass_of($permissionClass, ReferenceController::className())
                && in_array($permissionAction, ['Index', 'View', 'Update', 'Create', 'Delete', 'Restore'])) {
                if (!isset($data['reference'][$permissionClass])) {
                    $data['reference'][$permissionClass] = [
                        'id'      => $permissionClass,
                        'name'    => $permissionDescriptionClass,
                        'Index'   => false,
                        'View'    => false,
                        'Update'  => false,
                        'Create'  => false,
                        'Delete'  => false,
                        'Restore' => false,
                    ];
                }
                $data['reference'][$permissionClass][$permissionAction] = $isAssigned;
                continue;
            }
            if (is_subclass_of($permissionClass, DocumentController::className())
                && in_array($permissionAction, ['Index', 'View', 'Update', 'Create', 'Delete', 'Restore'])) {

                if (!isset($data['document'][$permissionClass])) {
                    $data['document'][$permissionClass] = [
                        'id'      => $permissionClass,
                        'name'    => $permissionDescriptionClass,
                        'Index'   => false,
                        'Update'  => false,
                        'Create'  => false,
                        'Delete'  => false,
                        'Restore' => false,
                    ];
                }
                $data['document'][$permissionClass][$permissionAction] = $isAssigned;
                continue;
            }
            $data['other'][] = [
                'id'         => $permission->name,
                'name'       => $permission->description,
                'isAssigned' => $isAssigned,
            ];
        }
        return [
            'reference' => new ArrayDataProvider([
                'allModels'  => array_values($data['reference']),
                'pagination' => false,
                'sort'       => [
                    'defaultOrder' => [
                        'name' => SORT_ASC,
                    ],
                    'attributes' => [
                        'name' => [
                            'default' => SORT_ASC,
                        ],
                    ],
                ]
            ]),
            'document'  => new ArrayDataProvider([
                'allModels'  => array_values($data['document']),
                'pagination' => false,
                'sort'       => [
                    'defaultOrder' => [
                        'name' => SORT_ASC,
                    ],
                    'attributes' => [
                        'name' => [
                            'default' => SORT_ASC,
                        ],
                    ],
                ]
            ]),
            'other'     => new ArrayDataProvider([
                'allModels'  => $data['other'],
                'pagination' => false,
                'sort'       => [
                    'defaultOrder' => [
                        'name' => SORT_ASC,
                    ],
                    'attributes' => [
                        'name' => [
                            'default' => SORT_ASC,
                        ],
                    ],
                ]
            ])
        ];
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributes = null, $onlyIfChanged = false)
    {
        if ($onlyIfChanged && $this->getDirtyAttributes(null, false)) {
            // Если при нестрогом сравнении не осталось изменившихся реквизитов, то не сохраняем модель
            return true;
        }
        $exception = null;

        if ($runValidation) {
            if (!$this->validate($attributes)) {
                return false;
            }
        }
        $transaction = static::getDb()->beginTransaction();
        try {
            if ($this->isNewRecord) {
                $name = str_pad((new Query())
                        ->select("MAX(name)")
                        ->from(static::tableName())
                        ->andWhere(['type' => \yii\rbac\Role::TYPE_ROLE])
                        ->andWhere('name != :name', [':name' => DbManager::ADMIN_ROLE])
                        ->scalar() + 1, 6, '0', STR_PAD_LEFT);
                $this->name = $name;
                $role = Yii::$app->authManager->createRole($this->name);
                $role->description = $this->description;
                $result = Yii::$app->authManager->add($role);
            } else {
                $role = Yii::$app->authManager->getRole($this->name);
                $role->description = $this->description;
                $result = Yii::$app->authManager->update($role->name, $role);
            }

            // Назначаем права
            $oldAssignedPermissions = Yii::$app->authManager->getPermissionsByRole($role->name);
            $permissionsForRemove = array_diff_key($oldAssignedPermissions, array_flip($this->assigned));
            $permissionsForAdd = array_diff($this->assigned, array_keys($oldAssignedPermissions));
            foreach ($permissionsForRemove as $permission) {
                Yii::$app->authManager->removeChild($role, $permission);
            }
            foreach ($permissionsForAdd as $permission) {
                $permission = Yii::$app->authManager->getPermission($permission);
                if ($permission) {
                    Yii::$app->authManager->addChild($role, $permission);
                }
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $result = false;
            $exception = $ex;
        }
        if (!$result) {
            if ($exception === null) {
                $exceptionMessage = 'Ошибка сохранения';
                if ($this->hasErrors()) {
                    $exceptionMessage = 'Ошибки сохранения:';
                    foreach ($this->getErrors() as $attributeErrors) {
                        foreach ($attributeErrors as $attributeError) {
                            $exceptionMessage .= PHP_EOL . $attributeError;
                        }
                    }
                }
                $exception = new UserException($exceptionMessage);
            } else {
                if ($exception instanceof UserException) {
                    $errorMessage = $exception->getMessage();
                } else {
                    Yii::error((string)$exception);
                    $errorMessage = 'Ошибка сервера';
                }
                if (!in_array($errorMessage, $this->getErrors(''))) {
                    $this->addError('summary', $errorMessage);
                }
            }
            throw $exception;
        }
        return $result;
    }

    /**
     * Магическая функция приведения объекта к строке
     * @return string
     */
    public function __toString()
    {
        return $this->isNewRecord ? '(новый)' : $this->description;
    }
}