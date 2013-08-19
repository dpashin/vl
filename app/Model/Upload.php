<?php
namespace Model;

class Upload extends \Kernel\Model
{
    public static $tablename = 'uploads';
    public static $columns = ['id', 'user_id', 'filename', 'thumbname'];

    private $uploadDir;
    const THUMBNAIL_MAX_WIDTH = 150;
    const THUMBNAIL_MAX_HEIGHT = 150;

    public static function uploadDir()
    {
        return realpath(__DIR__ . '/../../public/uploads');
    }

    public static function processUpload()
    {
        $result = [];

        for ($i = 0; $i < count($_FILES['file']['tmp_name']); $i++) {
            $name = $_FILES['file']['name'][$i];
            $error = $_FILES['file']['error'][$i];

            if (in_array($error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE])) {
                $result[] = ['error' => 'Слишком большой файл'];
                continue;
            } elseif ($error == UPLOAD_ERR_NO_FILE) {
                $result[] = ['error' => 'Файлы не загружены'];
                continue;
            } elseif ($error != UPLOAD_ERR_OK) {
                $result[] = ['error' => 'Ошибка при загрузке файла ' . $name];
                continue;
            }

            $tempName = $_FILES['file']['tmp_name'][$i];

            $tempThumb = tempnam(sys_get_temp_dir(), 'vl_');
            if (!self::createThumbnail($tempName, $tempThumb)) {
                $result[] = ['error' => 'Файл не является картинкой: ' . $name];
                unlink($tempThumb);
                continue;
            }

            $upload = new self();

            $suffix = uniqid(true) . '-' . $name;
            $upload->filename = 'img-' . $suffix;
            if (!move_uploaded_file($tempName, self::uploadDir() . '/' . $upload->filename)) {
                $result[] = ['error' => 'Ошибка при сохранении файла ' . $name];
                continue;
            }

            $upload->thumbname = 'thumb-' . $suffix;
            if (!rename($tempThumb, self::uploadDir() . '/' . $upload->thumbname)) {
                $result[] = ['error' => 'Ошибка при сохранении превью: ' . $name];
                unlink($tempThumb);
                unlink(self::uploadDir() . '/' . $upload->filename);
                continue;
            }
            chmod(self::uploadDir() . '/' . $upload->thumbname, 0644);

            $upload->user_id = \Kernel\App::identity()->get();
            $upload->create();

            $result[] = [
                'filename' => $upload->filename,
                'thumbname' => $upload->thumbname,
            ];
        }
        return $result;
    }

    protected static function createThumbnail($source, $target)
    {
        try {
            $image = new \Imagick($source);
            $image->resizeImage(150, 150, \Imagick::FILTER_LANCZOS, 0.9, true);
            $image->writeImage($target);
        } catch (\ImagickException $e) {
            return false;
        }
        return true;
    }

}