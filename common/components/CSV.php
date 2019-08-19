<?php
/**
 * Фаил для генерации CSV файлов с корректным их открытием в Excel
 */

namespace common\components;

use Yii;
use yii\web\Response;

class CSV
{
    /**
     * @var string имя временного файла
     */
    private $_tempFileName;

    /**
     * @var resource файловый указатель на временный фаил
     */
    private $_fd;

    /**
     * @var int счетчик строк
     */
    private $_rowCounter = 0;

    /**
     * CSV конструктор
     */
    public function __construct()
    {
        $this->_tempFileName = tempnam(sys_get_temp_dir(), 'csv');
        $this->_fd = fopen($this->_tempFileName, 'w');
    }

    /**
     * Деструктор
     */
    public function __destruct()
    {
        if (file_exists($this->_tempFileName)) {
            unlink($this->_tempFileName);
        }
    }

    /**
     * Запись строки в CSV
     *
     * @param array  $fields
     * @param string $fieldDelimiter
     * @param string $enc
     * @param string $escape
     * @param string $recordDelimiter
     * @param boolean $forceTextMode disable Excel type detection (https://stackoverflow.com/questions/165042/stop-excel-from-automatically-converting-certain-text-values-to-dates)
     * @return bool|int
     */
    public function writeRow($fields, $fieldDelimiter = ';', $enc = '"', $escape = '\\', $recordDelimiter = "\n", $forceTextMode = true)
    {
        foreach ($fields as &$field) {
            if ($this->_rowCounter == 0) {
                if (trim($field) == 'ID') {
                    $field = 'Id';
                }
            }
            $field = mb_convert_encoding($field, 'CP1251', 'UTF-8');
        }
        $fields = array_map(function($value) use ($enc, $escape, $forceTextMode) {
            $value = str_replace($enc, $escape . $enc, $value);
            return $forceTextMode ? '=""' .  $value . '""' : $value;
        }, $fields);
        $this->_rowCounter++;
        return fputs($this->_fd, $enc . implode($enc . $fieldDelimiter . $enc, $fields) . $enc . $recordDelimiter);
    }

    /**
     * Отправка файла клиенту
     *
     * @param string $fileName
     *
     * @return Response
     */
    public function output($fileName = 'export.csv')
    {
        fclose($this->_fd);
        return Yii::$app->response->sendFile($this->_tempFileName, $fileName, [
            'mimeType' => 'text/csv'
        ]);
    }

    /**
     * Сохранение результата в заданный фаил
     *
     * @param string $fileName полное имя файла вместе с путем до него
     */
    public function saveAs($fileName)
    {
        fclose($this->_fd);
        rename($this->_tempFileName, $fileName);
    }
}