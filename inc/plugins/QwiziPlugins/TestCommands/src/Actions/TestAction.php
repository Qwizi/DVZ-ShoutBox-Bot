<?php

namespace Qwizi\TestCommands\Actions;

use Qwizi\DVZSB\Actions\ActionInterface;

class TestAction implements ActionInterface
{
    public function execute($target, $additional = null)
    {
        echo $target.'test';
    }
}
