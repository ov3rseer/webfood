<?php

namespace common\models\reference;

use common\components\BinaryFile;
use common\components\ImageProcessor;
use common\components\PathFile;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\RGB;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Модель "Файл"
 *
 * @property string                             $name_full
 * @property string                             $path       путь для хранения файла
 * @property string                             $extension  расширение файла
 * @property string                             $comment
 * @property UploadedFile|PathFile|BinaryFile   $uploadFile
 */
class File extends Reference
{
    /**
     * @var UploadedFile|PathFile|BinaryFile загружаемый файл
     */
    protected $uploadFile;

    /**
     * Получение имени сущности в единственном числе
     * @return string
     */
    public function getSingularName()
    {
        return 'Файл';
    }

    /**
     * Получение имени сущности во множественном числе
     * @return string
     */
    public function getPluralName()
    {
        return 'Файлы';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['extension'], 'required'],
            [['extension'], 'string', 'max' => 10],
            [['comment', 'name_full'], 'string'],
            [['extension'], 'filter', 'filter' => function($ext) { return mb_strtolower(trim($ext)); }],
            [['path'], 'default', 'value' => ''],
            [['uploadFile'], 'required', 'when' => function() { return $this->isNewRecord; }],
            [['uploadFile'], 'validateUploadFile'],
        ]);
    }

    /**
     * Валидация поля uploadFile
     */
    public function validateUploadFile()
    {
        $uploadFile = $this->uploadFile;
        if ($uploadFile) {
            if (!($uploadFile instanceof UploadedFile || $uploadFile instanceof PathFile || $uploadFile instanceof BinaryFile)) {
                $this->addError('uploadFile', 'Неверный тип загружаемого файла');
            }
        }
    }

    /**
     * Установка поля uploadFile
     * @param UploadedFile|PathFile|BinaryFile $value
     */
    public function setUploadFile($value)
    {
        if ($this->uploadFile !== $value) {
            if ($value instanceof UploadedFile || $value instanceof PathFile || $value instanceof BinaryFile) {
                $this->name = $value->name;
                $this->name_full = $value->name;
                $this->extension = $value->extension;
                $this->uploadFile = $value;
            }
        }
    }

    /**
     * Возвращает значение поля uploadFile
     * @return UploadedFile|PathFile|BinaryFile
     */
    public function getUploadFile()
    {
        return $this->uploadFile;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name_full' => 'Заголовок',
            'path'      => 'Подкаталог',
            'extension' => 'Расширение',
            'comment'   => 'Комментарий',
        ]);
    }

    /**
     * Возвращает путь до папки с оригинальным файлом
     * @return string
     */
    public function getOriginalDir()
    {
        return Yii::getAlias('@uploads/' . ltrim($this->path . '/', '/'));
    }

    /**
     * Возвращает путь до папки с миниатюрами изображения
     * @param boolean $asUrl возвращать URL вместо файлового пути
     * @return string
     */
    public function getThumbsDir($asUrl = false)
    {
        return Yii::getAlias(($asUrl ? '@web' : '@webroot') . '/files/' . ltrim($this->path . '/', '/') . $this->id);
    }

    /**
     * Возвращает путь до оригинального файла
     * @return string
     */
    public function getOriginalPath()
    {
        return $this->getOriginalDir() . "{$this->id}.{$this->extension}";
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->uploadFile) {
            FileHelper::createDirectory($this->getOriginalDir());
            $this->uploadFile->saveAs($this->getOriginalPath());
        }
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        FileHelper::removeDirectory($this->getThumbsDir());
        if (file_exists($this->getOriginalPath()) && !unlink($this->getOriginalPath())) {
            throw new Exception('Не удалось удалить файл');
        }
    }

    /**
     * Проверка является ли текущий файл изображением (по расширению)
     * @return boolean
     */
    public function isImage()
    {
        $extensions = ['jpeg', 'jpg', 'png', 'bmp', 'gif', 'svg'];
        return in_array(mb_strtolower($this->extension), $extensions);
    }

    /**
     * Проверка является ли текущий файл векторным изображением (по расширению)
     * @return boolean
     */
    public function isVectorImage()
    {
        $extensions = ['svg'];
        return in_array(mb_strtolower($this->extension), $extensions);
    }

    /**
     * Публикация файла
     * @param boolean $returnPath возвращать путь к файлу вместо ссылки
     * @param array $options опции для публикации изображения
     *        integer $width            ширина изображения, если не задано будет использована ширина исходного изображения
     *        integer $height           высота изображения, если не задано будет использована высота исходного изображения
     *        string  $background       цвет фона
     *        integer $alpha            прозрачность фона
     *        boolean $disableAnimation выключить анимацию (для GIF-изображений)
     *        boolean $crop             обрезать изображение для сохранения пропорций
     *        boolean $returnPath       возвращать путь к файлу вместо ссылки
     * @return string
     * @throws Exception
     */
    public function publish($returnPath = false, $options = [])
    {
        if (!file_exists($this->getOriginalPath())) {
            return null;
        }
        $src = $this->getOriginalPath();
        $publishDir = $this->getThumbsDir(false);
        FileHelper::createDirectory($publishDir, Yii::$app->assetManager->dirMode, true);
        $publishFileName = $this->getFileNameForPublishOptions($options) . '.' . $this->extension;
        $publishPath = $publishDir . DIRECTORY_SEPARATOR . $publishFileName;
        $publishUrl = $this->getThumbsDir(true) . '/' . $publishFileName;
        if ($this->isImage() && !$this->isVectorImage()) {
            $width = empty($options['width']) ? 0 : (int)$options['width'];
            $height = empty($options['height']) ? 0 : (int)$options['height'];
            $background = empty($options['background']) ? 'fff' : $options['background'];
            $alpha = isset($options['alpha']) && $options['alpha'] !== '' ? (int)$options['alpha'] : 100;
            $disableAnimation = !empty($options['disableAnimation']);
            $crop = !empty($options['crop']);
            $backgroundColor = (new RGB())->color($background, $alpha);
            if (!file_exists($publishPath) || @filemtime($publishPath) < @filemtime($src)) {
                $color = 'rgba(' .
                    $backgroundColor->getValue(ColorInterface::COLOR_RED) . ',' .
                    $backgroundColor->getValue(ColorInterface::COLOR_GREEN) . ',' .
                    $backgroundColor->getValue(ColorInterface::COLOR_BLUE) . ',' .
                    round((100 - $backgroundColor->getAlpha()) / 100, 1) .
                    ')';
                (new ImageProcessor())->resize($src, $publishPath, [$width, $height], $color, $crop, $disableAnimation);
            }
        } else {
            if (@filemtime($publishPath) < @filemtime($src)) {
                copy($src, $publishPath);
                if (Yii::$app->assetManager->fileMode !== null) {
                    @chmod($publishPath, Yii::$app->assetManager->fileMode);
                }
            }
        }
        $publishUrl .= (isset($options['mtime']) && $options['mtime'] === false ? '' : '?v=' . $this->update_date->format('U'));
        return $returnPath ? $publishPath : $publishUrl;
    }

    /**
     * Получение настроек публикации файла из его имени
     * @param string $name наименование файла (без расширения)
     * @return array настройки публикации
     */
    public function getPublishOptionsForFileName($name)
    {
        $result = [];
        if ($this->isImage() && !$this->isVectorImage()) {
            $parts = explode('_', $name);
            $result['width'] = !empty($parts[0]) ? $parts[0] : 0;
            $result['height'] = !empty($parts[1]) ? $parts[1] : 0;
            $result['background'] = !empty($parts[2]) ? $parts[2] : 'fff';
            $result['alpha'] = isset($parts[3]) && $parts[3] !== '' ? (int)$parts[3] : 100;
            $result['disableAnimation'] = isset($parts[4]) && $parts[4] !== '' ? $parts[4] : false;
            $result['crop'] = isset($parts[5]) && $parts[5] !== '' ? $parts[5] : false;
        }
        return $result;
    }

    /**
     * Получение имени файла, соответствующего указанным настройкам публикации
     * @param array $options настройки публикации
     * @return string
     */
    public function getFileNameForPublishOptions($options)
    {
        $result = 'file';
        if ($this->isImage() && !$this->isVectorImage()) {
            $result =
                (!empty($options['width']) ? (int)$options['width'] : 0) .
                '_' .
                (!empty($options['height']) ? (int)$options['height'] : 0) .
                '_' .
                (!empty($options['background']) ? $options['background'] : 'fff') .
                '_' .
                (isset($options['alpha']) && $options['alpha'] !== '' ? (int)$options['alpha'] : 100) .
                '_' .
                (!empty($options['disableAnimation']) ? 1 : 0) .
                '_' .
                (!empty($options['crop']) ? 1 : 0);
        }
        return $result;
    }
}