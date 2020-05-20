<?php


namespace App\Converters;


use App\Contracts\Converter;
use App\Entity\Car;

class CarConverter extends Converter
{

    /**
     * @param Car $object
     * @return array
     */
    public function convert($object): array
    {
        $carGroup = $object->getCarGroup();
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
            'price' => $object->getPrice(),
            'class' => $object->getCarClass(),
            'color' => $object->getColor(),
            'places' => $object->getPlaces(),
            'slug' => $object->getSlug(),
            'group' => [
                'id' => $carGroup !== null ? $carGroup->getId() : -1,
                'name' => $carGroup !== null ? $carGroup->getName() : ''
            ],
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
