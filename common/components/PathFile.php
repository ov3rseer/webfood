<?php

namespace common\components;

use yii\base\BaseObject;

/**
 * Компонент для загрузки файла с помощью пути
 */
class PathFile extends BaseObject
{
    /**
     * @var string путь к файлу
     */
    public $path;

    /**
     * @var string имя файла
     */
    public $name;

    /**
     * @var string расширение файла
     */
    public $extension;

    /**
     * @var boolean флаг удаления исходного файла после сохранения нового файла
     */
    public $deleteAfterSave;

    /**
     * @var integer (для совместимости с UploadedFile)
     */
    public $error;

    /**
     * Получение экземпляра текущего компонента по пути к файлу
     * @param string $path путь к файлу
     * @param boolean $deleteAfterSave удалить файл после сохранения нового файла
     * @throws \InvalidArgumentException
     * @return static
     */
    public static function getInstance($path, $deleteAfterSave = false)
    {
        if (!is_readable($path)) {
            throw new \InvalidArgumentException('Указанный файл отсутствует или недоступен для чтения: ' . $path);
        }
        $pathInfo = pathinfo($path);
        $object = new static([
            'path'            => $path,
            'name'            => $pathInfo['filename'],
            'extension'       => isset($pathInfo['extension']) ? $pathInfo['extension'] : '',
            'deleteAfterSave' => $deleteAfterSave,
        ]);
        return $object;
    }

    /**
     * Сохраняет загружаемый файл в требуемое место
     * @param string $path путь по которому надо сохранить загружаемый файл
     * @return boolean результат сохранения
     */
    public function saveAs($path)
    {
        if ($this->deleteAfterSave) {
            return rename($this->path, $path);
        } else {
            return copy($this->path, $path);
        }
    }
}
