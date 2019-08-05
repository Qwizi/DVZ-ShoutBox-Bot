<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Interfaces;

interface CommandInterface
{
    function pattern(string $commandData): string;
    function doAction(array $data): void;
}