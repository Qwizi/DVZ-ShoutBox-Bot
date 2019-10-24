<?php

namespace Qwizi\DVZSB\Interfaces;

interface ActionInterface
{
    public function execute($target, array $additional);
}