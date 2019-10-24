<?php
declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

use Qwizi\DVZSB\Interfaces\PaginationInterface;

class Pagination implements PaginationInterface
{
    /**
     * @var $perPage
     */
    private $perPage = 10;

    /**
    * Set the per page number
    *
    * @param int $perPageValue Value of the page number
    * @return void
    */
    public function setPerPage(int $perPageValue): void
    {
        $this->perPage = $value;
    }

    /**
     * Get the per page number
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Paginate the data
     *
     * @param array $data Data
     * @param int $page Page
     * @return array
     */
    public function paginate(array $data, int $page): array
    {
        $perPage = $this->getPerPage();
        $page = $page < 1 ? 1 : $page;
        $start = ($page - 1) * ($perPage + 1);
        $offset = $perPage + 1;

        return array_slice($data, $start, $offset);
    }
}
