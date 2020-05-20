<?php


namespace App\Converters;


use App\Contracts\Converter;
use App\Entity\WineTour;

class WineTourConverter extends Converter
{

    /**
     * @param WineTour $object
     * @return array
     */
    public function convert($object): array
    {
        $neededNames = [
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

        return [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'slug' => $object->getSlug(),
            'price' => $object->getPrice(),
            'photos' => array_map(function ($data) use ($neededNames) {


                $toResponse = [];

                foreach ($data['files'] as $key => $data) {
                    if (in_array($key, $neededNames)) {
                        $toResponse[$key] = '/uploads/photos/' . $data['filename'];
                    }
                }

                return $toResponse;
            }, $object->getPhotos())
        ];
    }

}
