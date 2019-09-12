<?php
declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

class Pagination
{
    private $perPage = 10;

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function setPerPage($value)
    {
        $this->perPage = $value;
    }

    public function paginate(array $dataArray, int $page)
    {
        $perPage = $this->getPerPage();
        $page = $page < 1 ? 1 : $page;
        $start = ($page - 1) * ($perPage + 1);
        $offset = $perPage + 1;

        return array_slice($dataArray, $start, $offset);
    }
}
