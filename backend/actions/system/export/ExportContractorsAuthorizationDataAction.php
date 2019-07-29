<?php

namespace backend\actions\system\export;

use common\components\DateTime;
use common\models\enum\UserType;
use common\models\reference\Contractor;
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
 * Действие для выгрузки авторизационных данных новых контрагентов
 */
class ExportContractorsAuthorizationDataAction extends Action
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
        /** @var Contractor[] $contractors */
        $contractors = Contractor::find()->andWhere(['is_active' => true, 'user_id' => null])->all();

        if ($contractors) {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getDefaultStyle()
                ->getFont()
                ->setSize(18)
                ->setName('Arial');
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Выгрузка авторизационных данных')
                ->freezePane('A2')
                ->setShowGridlines(true)
                ->getPageSetup()
                ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
                ->setPaperSize(PageSetup::PAPERSIZE_A4);

            $rowIndex = 1;
            $rows[$rowIndex] = [
                'A' => 'Код контрагента',
                'B' => 'Контрагент',
                'C' => 'Логин',
                'D' => 'Пароль',
            ];

            foreach ($contractors as $contractor) {
                if ($contractor->user) {
                    continue;
                }
                $rowIndex++;

                $login = 'contr' . time() . $rowIndex;
                $password = 'pass' . time() . $rowIndex;

                $user = new User();
                $user->name = $login;
                $user->name_full = (string)$contractor;
                $user->is_active = true;
                $user->user_type_id = UserType::CONTRACTOR;
                $user->setPassword($password);
                $user->generateAuthKey();
                $user->generateEmailVerificationToken();
                $user->save();

                $contractor->user_id = $user->id;
                $contractor->save();
                $rows[$rowIndex] = ['A' => $contractor->contractor_code, 'B' => htmlspecialchars_decode(Html::encode((string)$contractor)), 'C' => $login, 'D' => $password];
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
            $filename = 'Выгрузка авторизационных данных контрагентов от ' . $currentDate->format('d.m.Y');
            $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
            $writer->save("php://output");
        } else {
            Yii::$app->session->setFlash('info', 'Не найдены новые контрагенты.');
            $this->controller->redirect('index');
        }
    }
}