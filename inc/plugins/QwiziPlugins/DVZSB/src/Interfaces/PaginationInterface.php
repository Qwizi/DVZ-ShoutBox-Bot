<?php

namespace Qwizi\DVZSB\Interfaces;

interface PaginationInterface
{
    public function setPerPage(array $data): void;
    public function getPerPage(): int;
    public function paginate(array $data, int $page): array;
}
