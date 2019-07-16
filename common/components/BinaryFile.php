<?php

namespace common\components;

use yii\base\BaseObject;

/**
 * Компонент для загрузки файла с помощью бинарных данных
 */
class BinaryFile extends BaseObject
{
    /**
     * @var string бинарные данные файла
     */
    public $binary;

    /**
     * @var string имя файла
     */
    public $name;

    /**
     * @var string расширение файла
     */
    public $extension;

    /**
     * @var integer (для совместимости с UploadedFile)
     */
    public $error;

    /**
     * Получение экземпляра текущего компонента по бинарному коду файла
     * @param string $binary бинарный код файла
     * @param string $name   имя файла
     * @return static
     */
    public static function getInstance($binary, $name)
    {
        $nameParts = mb_strrpos($name, '.');
        $object = new static([
            'binary'    => $binary,
            'name'      => substr($name, 0, $nameParts),
            'extension' => substr($name, $nameParts + 1),
        ]);
        return $object;
    }

    /**
     * Сохраняет загружаемый файл в требуемое место
     * @param string $path путь по которому надо сохранить загружаемый файл
     */
    public function saveAs($path)
    {
        file_put_contents($path, $this->binary);
    }
}
