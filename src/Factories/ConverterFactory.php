<?php


namespace App\Factories;


use App\Converters\CarConverter;
use App\Converters\CarGroupConverter;
use App\Converters\WineTourConverter;
use App\Entity\Car;
use App\Entity\CarGroup;
use App\Entity\WineTour;
use App\Services\ConverterService;

class ConverterFactory
{

    public function __invoke()
    {
        $converterService = new ConverterService();

        $converterService->bind(Car::class, CarConverter::class);
        $converterService->bind(CarGroup::class, CarGroupConverter::class);
        $converterService->bind(WineTour::class, WineTourConverter::class);

        return $converterService;
    }

}
