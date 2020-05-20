<?php


namespace App\Services;


use Knp\Component\Pager\Pagination\PaginationInterface;

class AutoResponse
{

    /**
     * @param array|PaginationInterface $objects
     * @return array
     */
    public function format($objects)
    {
        if (is_array($objects)) {
            return $this->formatArray($objects);
        } else if ($objects instanceof PaginationInterface) {
            return $this->formatPagination($objects);
        } else {
            return [  ];
        }
    }

    private function formatArray($objects) {
        return $objects;
    }

    /**
     * @param PaginationInterface $objects
     * @return array
     */
    private function formatPagination(PaginationInterface $objects)
    {
        return [
            'data' => $objects,
            'pagination' => [
                'page' => $objects->getCurrentPageNumber(),
                'pages' => $objects->getTotalItemCount(),
                'on_page' => $objects->count()
            ]
        ];
    }

}
