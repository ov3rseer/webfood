<?php

namespace backend\actions\system\export;

use common\components\DateTime;
use common\helpers\StringHelper;
use common\models\enum\UserType;
use common\models\reference\ServiceObject;
use common\models\reference\User;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\base\UserException;
use yii\helpers\Html;

/**
 * Действие для выгрузки авторизационных данных новых объектов обслуживания
 */
class ExportObjectAuthorizationDataAction extends Action
{
    /**
     * @inheritdoc
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \yii\base\Exception
     * @throws UserException
     */
    public function run()
    {
        /** @var ServiceObject[] $serviceObjects */
        $serviceObjects = ServiceObject::find()->andWhere(['user_id' => null])->all();

        if ($serviceObjects) {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setSize(18)
                ->setName('Arial');
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Выгрузка данных авторизации')
                ->freezePane('A2')
                ->setShowGridlines(true)
                ->getPageSetup()
                ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
                ->setPaperSize(PageSetup::PAPERSIZE_A4);

            $rowIndex = 1;
            $rows[$rowIndex] = [
                'A' => 'Объект обслуживания',
                'B' => 'Логин',
                'C' => 'Пароль',
                'D' => 'Город',
                'E' => 'Адрес',
            ];

            foreach ($serviceObjects as $serviceObject) {
                if ($serviceObject->user) {
                    continue;
                }
                $rowIndex++;

                $serviceObjectMaxId = ServiceObject::find()->max('id');
                $login = 'object' . $serviceObjectMaxId . $rowIndex;
                $password = StringHelper::generatePassword(10);

                $user = new User();
                $user->name = $login;
                $user->name_full = (string)$serviceObject;
                $user->is_active = true;
                $user->user_type_id = UserType::SERVICE_OBJECT;
                $user->setPassword($password);
                $user->generateAuthKey();
                $user->generateEmailVerificationToken();
                $user->save();

                $serviceObject->user_id = $user->id;
                $serviceObject->save();
                $rows[$rowIndex] = [
                    'A' => htmlspecialchars_decode(Html::encode((string)$serviceObject)),
                    'B' => $login,
                    'C' => $password,
                    'D' => $serviceObject->city,
                    'E' => $serviceObject->zip_code . ', ' . $serviceObject->address,
                ];
            }

            foreach ($rows as $rowIndex => $row) {
                foreach ($row as $columnIndex => $cell) {
                    $sheet->setCellValue($columnIndex . $rowIndex, $cell);
                    $sheet->getColumnDimension($columnIndex)->setWidth(20);
                    $borders = $sheet->getStyle($columnIndex . $rowIndex)->getBorders();
                    $borders->getTop()->setBorderStyle(Border::BORDER_THIN);
                    $borders->getBottom()->setBorderStyle(Border::BORDER_THIN);
                    $borders->getLeft()->setBorderStyle(Border::BORDER_THIN);
                    $borders->getRight()->setBorderStyle(Border::BORDER_THIN);
                }
            }
            $currentDate = new DateTime('now');
            $filename = 'Выгрузка авторизационных данных объектов обслуживания от ' . $currentDate->format('d.m.Y');
            $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
            $writer->save("php://output");
        } else {
            Yii::$app->session->setFlash('info', 'Не найдены новые объекты обслуживания.');
            $this->controller->redirect('index');
        }
    }
}