<?php
declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

use Qwizi\DVZSB\Actions\AbstractAction;

class PaginateAction extends AbstractAction
{
    /**
     * @var $perPage
     */
    private $perPage = 10;

    /**
     * Paginate the data
     *
     * @param array $data Data
     * @param int $page Page
     * @return array
     */
    public function execute($target, $additional = null)
    {
        $perPage = $this->perPage;
        $page = $aditional['page'] < 1 ? 1 : $page;
        $start = ($page - 1) * ($perPage + 1);
        $offset = $perPage + 1;

        return array_slice($target, $start, $offset);
    }
}
