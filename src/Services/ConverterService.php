<?php


namespace App\Services;


use App\Contracts\Converter;
use Exception;

class ConverterService
{

    private $map = [];

    public function bind($entityType, $converter, string $scope = 'default'): self
    {
        if (!array_key_exists($entityType, $this->map)) {
            $this->map[$entityType] = [];
        }
        $this->map[$entityType][$scope] = $converter;

        return $this;
    }

    /**
     * @param $entityType
     * @param string $scope
     * @return Converter
     * @throws Exception
     */
    public function get($entityType, string $scope = 'default'): Converter
    {
        if (array_key_exists($entityType, $this->map) && array_key_exists($scope, $this->map[$entityType])) {
            /** @var Converter $converter */
            $converter = new $this->map[$entityType][$scope]();
            if (!empty($converter->getDependencies())) {
                $dependencies = $converter->getDependencies();
                foreach ($dependencies as $dependency) {
                    $dependencyType = $dependency;
                    $dependencyScope = 'default';

                    if (is_array($dependency)) {
                        $dependencyType = $dependency['type'];
                        $dependencyScope = $dependency['scope'];
                    }

                    $converter->saveDependency($dependency, $this->get($dependencyType, $dependencyScope));
                }
            }
            return $converter;
        }

        throw new Exception("Unknown entity type");
    }

}
