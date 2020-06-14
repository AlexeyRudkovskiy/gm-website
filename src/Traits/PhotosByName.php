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

    public function getPhotosByNames(array $names): ?array
    {
        $photos = $this->getPhotos() ?? [ ];
        $targetPhotos = [];
        foreach ($photos as $photo) {
            $temp = [];
            foreach ($photo['files'] as $name => $data) {
                if (in_array($name, $names)) {
                    $temp[$name] = $data['filename'];
                }
            }
            array_push($targetPhotos, $temp);
        }

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

    public function getOriginalPhoto()
    {
        $original = $this->image['original'];

        return [
            'filename' => $original['filename'],
            'width' => $original['width'],
            'height' => $original['height'],
            'aspect_ratio' => $original['aspect_ratio']
        ];
    }

}
