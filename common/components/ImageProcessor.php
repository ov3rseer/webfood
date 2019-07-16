<?php

namespace common\components;

use yii\base\Component;
use yii\base\Exception;

/**
 * Компонент для обработки изображений
 * Использует внешнюю программу "ImageMagick" (apt-get install imagemagick)
 */
class ImageProcessor extends Component
{
    /**
     * Получение размера изображения
     * @param string $path исходный файл
     * @return array
     * @throws Exception
     */
    public function getSize($path)
    {
        $result = 0;
        $output = [];
        exec('identify -quiet -format "%[P]\n" ' . escapeshellarg($path . '[0]') . ' 2>&1', $output, $result);
        if ($result != 0) {
            throw new Exception('Ошибка получения размера изображения: ' . implode(',', $output));
        }
        return explode('x', $output[0]);
    }

    /**
     * Изменение размера изображения
     * @param string $sourcePath       исходный файл
     * @param string $targetPath       целевой файл
     * @param array  $newSize          новый размер (массив [ширина, высота], если измерение = 0, то оно будет вычислено)
     * @param string $background       цвет фона в формате rgba (например, 'rgba(255,255,255,0)')
     * @param bool   $crop             обрезать изображение по минимальному измерению
     * @param bool   $disableAnimation выключить анимацию (для GIF-изображений)
     * @param int    $quality          качество изображения от 0 до 100. Если 0, то качество определяется автоматически
     * @throws Exception
     */
    public function resize($sourcePath, $targetPath, $newSize = [], $background = '', $crop = false, $disableAnimation = false, $quality = 0)
    {
        if (!$newSize[0] || !$newSize[1]) {
            $originalSize = $this->getSize($sourcePath);
            $originalAspectRatio = $originalSize[0] / $originalSize[1];
            if (!$newSize[0] && !$newSize[1]) {
                $newSize = $originalSize;
            } else if (!$newSize[0]) {
                $newSize[0] = round($newSize[1] * $originalAspectRatio);
            } else if (!$newSize[1]) {
                $newSize[1] = round($newSize[0] / $originalAspectRatio);
            }
        }
        $tmpImagePath = false;
        if (substr($sourcePath, -4) == '.gif') {
            $tmpImagePath = tempnam(sys_get_temp_dir(), 'gif_resize_');
            $result = 0;
            $output = [];
            exec('convert ' . escapeshellarg($sourcePath) . ' -coalesce ' . escapeshellarg($tmpImagePath) . ' 2>&1', $output, $result);
            if ($result != 0) {
                throw new Exception('Ошибка изменения размера изображения: ' . implode(',', $output));
            }
            $sourcePath = $tmpImagePath . ($disableAnimation ? '[0]' : '');
        }
        $result = 0;
        $output = [];
        exec(
            'convert -quiet -strip ' . escapeshellarg($sourcePath) .
            ' -resize ' . escapeshellarg($newSize[0] . 'x' . $newSize[1] . ($crop ? '^' : '')) .
            ' -background ' . escapeshellarg($background) .
            ' -gravity center' .
            ' -extent ' . escapeshellarg($newSize[0] . 'x' . $newSize[1]) .
            ($quality ? ' -quality ' . $quality : '') .
            ' ' . escapeshellarg($targetPath) . ' 2>&1', $output, $result
        );
        if ($tmpImagePath) {
            unlink($tmpImagePath);
        }
        if ($result != 0) {
            throw new Exception('Ошибка изменения размера изображения: ' . implode(',', $output));
        }
    }

    /**
     * Изменение формата изображения
     * @param string $sourcePath исходный файл (со старым разширением)
     * @param string $targetPath целевой файл (с новым расширением)
     * @throws Exception
     */
    public function convert($sourcePath, $targetPath)
    {
        $result = 0;
        $output = [];
        exec('convert -quiet -strip ' .
            escapeshellarg($sourcePath) . ' ' .
            escapeshellarg($targetPath) . ' ' .
            '2>&1', $output, $result);
        if ($result != 0) {
            throw new Exception('Ошибка конвертации изображения: ' . implode(',', $output));
        }
    }

    /**
     * Кодирование изображения
     * @param BinaryFile $binaryFile
     * @return string
     * @throws Exception
     */
    public function encodeBase64($binaryFile)
    {
        switch ($binaryFile->extension) {
            case 'jpeg':
            case 'jpg':
                $result = 'data:image/jpeg;base64,' . base64_encode($binaryFile->binary);
                break;
            case 'png':
                $result = 'data:image/png;base64,' . base64_encode($binaryFile->binary);
                break;
            case 'gif':
                $result = 'data:image/gif;base64,' . base64_encode($binaryFile->binary);
                break;
            default:
                throw new Exception('Неизвестное расширение файла изображения: ' . $binaryFile->extension);
        }
        return $result;
    }

    /**
     * Декодирование изображения
     * @param string $encodedString
     * @return BinaryFile
     * @throws Exception
     */
    public function decodeBase64($encodedString)
    {
        $prefix = substr($encodedString, 0, 22);
        switch ($prefix) {
            case 'data:image/jpeg;base64':
                $binary = base64_decode(substr($encodedString, 23));
                $extension = 'jpg';
                break;
            case 'data:image/png;base64,':
                $binary = base64_decode(substr($encodedString, 22));
                $extension = 'png';
                break;
            case 'data:image/gif;base64,':
                $binary = base64_decode(substr($encodedString, 22));
                $extension = 'gif';
                break;
            default:
                throw new Exception('Неизвестный формат кодирования изображения: ' . $prefix);
        }
        return BinaryFile::getInstance($binary, 'file.' . $extension);
    }
}
