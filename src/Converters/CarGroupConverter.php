<?php


namespace App\Converters;


use App\Contracts\Converter;
use App\Entity\CarGroup;

class CarGroupConverter extends Converter
{

    /**
     * @param CarGroup $object
     * @return array
     */
    public function convert($object): array
    {
        $neededKeys = [
            'large',
            'home-large',
            'home-small',
            'single-page-small',
            'large',
            'list-large',
            'car-large',
            'car-small',
            'configurator-card',
            'admin-preview',
        ];

        $photo = $object->getPhoto();
        if (!empty($photo)) {
            $photoFiles = $photo['files'];
            $photo = [];

            foreach ($photoFiles as $key => $file) {
                if (!in_array($key, $neededKeys)) {
                    continue;
                }
                $photo[$key] = '/uploads/photos/' . $file['filename'];
            }
        }

        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'slug' => $object->getSlug(),
            'description' => $object->getDescription(),
            'photo' => $photo
        ];
    }

}
