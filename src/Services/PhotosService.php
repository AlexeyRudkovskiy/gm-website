<?php


namespace App\Services;


use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotosService
{

    protected $sizes = [];

    /**
     * @var ImageManager
     */
    protected $imageManager;

    public function __construct($sizes, $driver)
    {
        $this->sizes = $sizes;
        $this->imageManager = new ImageManager([ 'driver' => $driver ]);
    }

    /**
     * @param UploadedFile $file
     * @param string $originalPath
     * @param string $targetDirectory
     * @return UploadedPhoto[]
     */
    public function processPhoto(UploadedFile $file, string $originalPath, string $targetDirectory)
    {
        $uploadedPhotos = [];

        foreach ($this->sizes as $index => $size) {
            $img = $this->imageManager->make($originalPath);
            $name = $size['name'];
            $width = $size['width'];
            $height = $size['height'];
            $crop = $size['crop'];
            $resize = $size['resize'];
            $fit = $size['fit'];

            if ($resize === 1) {
                $img = $img->resize($width, $height);
            } else if ($crop === 1) {
                $img = $img->crop($width, $height);
            } else if ($fit === 1) {
                $img = $img->fit($width, $height);
            }

            $ext = $file->getClientOriginalExtension();
            $filename = sha1($originalPath) . '-' . uniqid() . '-' . $name . '.' . $ext;
            $img->save($targetDirectory . '/' . $filename);

            $uploadedPhoto = new UploadedPhoto();
            $uploadedPhoto->setDirectory($targetDirectory);
            $uploadedPhoto->setFilename($filename);
            $uploadedPhoto->setSize($name);

            array_push($uploadedPhotos, $uploadedPhoto);
        }

        return $uploadedPhotos;
    }

    public function genOne($width, $height, $top, $left, $scale, $filename, $owidth, $oheight)
    {
    }

    public function regenerate(array $originalFiles, array $update, $original)
    {
        $oheight = $original['height'];
        $owidth = $original['width'];
        $filename = $original['filename'];

        foreach ($update as $name => $data) {
            $target = $originalFiles[$name];

            $scale = $data['scale'];
            $width = $data['width'];
            $height = $data['height'];
            $top = $data['top'];
            $left = $data['left'];

            $file = $this->imageManager->make('/mnt/g/Projects/PhpStormProjects/gm.local/var/uploads/original/' . $filename);
            $scale = intval($scale) / 100;

            $file
                ->resize($owidth / $scale, $oheight / $scale)
                ->crop($width, $height, intval($left / $scale), intval($top / $scale))
                ->save($target['directory'] . '/' . $target['filename']);
        }
    }

    public function getSizes()
    {
        return $this->sizes;
    }

    /**
     * @return ImageManager
     */
    public function getImageManager(): ImageManager
    {
        return $this->imageManager;
    }

}
