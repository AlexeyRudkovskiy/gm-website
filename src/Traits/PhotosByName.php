<?php


namespace App\Traits;


trait PhotosByName
{

    public function getPhotosByName(string $name): ?array
    {
        $photos = $this->getPhotos() ?? [ ];
        $targetPhotos = [];
        foreach ($photos as $photo) {
            array_push($targetPhotos, $photo['files'][$name] ?? '');
        }

        $targetPhotos = array_map(function ($photo) {
            return $photo['filename'];
        }, $targetPhotos);

        return $targetPhotos;
    }

    public function getImageByName(string $name): ?string
    {
        $image = $this->getImage();
        if ($image === null || empty($image)) {
            return null;
        }

        $files = $image['files'];
        $file = $files[$name] ?? ['filename' => null];

        return $file['filename'];
    }

}
