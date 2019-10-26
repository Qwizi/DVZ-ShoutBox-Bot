<?php

namespace Qwizi\DVZSB\Actions;

interface ActionInterface
{
    public function execute($target, $additional = null);
}