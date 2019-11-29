<?php

namespace backend\models\system;

use common\components\DateTime;
use common\models\enum\ConsoleTaskStatus;
use common\models\enum\ConsoleTaskType;
use common\models\reference\ConsoleTask;
use common\models\reference\File;
use Yii;
use yii\base\UserException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * Форма для импорта поставщиков продуктов
 *
 * @property File $file
 * @property UploadedFile[] $uploadedFiles
 * @property integer $file_id
 */
class ImportProductProviderForm extends ImportForm
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Импорт поставщиков продуктов';
    }

    /**
     * @inheritdoc
     * @throws UserException
     */
    public function proceed()
    {
        parent::proceed();
        $consoleTaskType = ConsoleTaskType::findOne(ConsoleTaskType::IMPORT_PRODUCT_PROVIDER);
        if ($this->file_id && $consoleTaskType) {
            $consoleTask = new ConsoleTask();
            $consoleTask->type_id = $consoleTaskType->id;
            $consoleTask->is_repeatable = false;
            $consoleTask->name = (string)$consoleTaskType;
            $consoleTask->status_id = ConsoleTaskStatus::PLANNED;
            $consoleTask->params = Json::encode([
                'files_id' => $this->file_id,
            ]);
            $consoleTask->start_date = new DateTime('now');
            $consoleTask->save();
            Yii::$app->session->setFlash('success',
                'Файл будет загружен в ближайшее время. Статус загрузки можно просмотреть в отчете ' . Html::a('Задачи', ['/report/tasks'], ['target' => '_blank']) . '.'
            );
        }
    }
}