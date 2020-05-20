<?php


namespace App\Contracts;


use Knp\Component\Pager\Pagination\PaginationInterface;

abstract class Converter
{

    /** @var array  */
    private $dependencies = [];

    public abstract function convert($object): array;

    /**
     * @param array|PaginationInterface $objects
     * @return array|PaginationInterface
     */
    public function convertArray($objects)
    {
        if (is_array($objects)) {
            return $this->convertEachItem($objects);
        } else if ($objects instanceof PaginationInterface) {
            $items = $objects->getItems();
            $items = $this->convertEachItem($items);
            $objects->setItems($items);
            return [
                'data' => $objects,
                'pagination' => [
                    'per_page' => $objects->getItemNumberPerPage(),
                    'page' => $objects->getCurrentPageNumber()
                ]
            ];
        }

        return null;
    }

    /**
     * @param array|iterable $items
     * @return array
     */
    public function convertEachItem($items): array
    {
        return array_map(function ($item) {
            return $this->convert($item);
        }, $items);
    }

    public function getDependencies()
    {
        return [];
    }

    /**
     * @param $key
     * @return Converter
     */
    protected function getDependency($key)
    {
        return $this->dependencies[$key];
    }

    /**
     * @param $key
     * @param Converter $object
     */
    public function saveDependency($key, Converter $object)
    {
        $this->dependencies[$key] = $object;
    }

}
