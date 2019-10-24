<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use Qwizi\DVZSB\Interfaces\ActionInterface;

class CommandAction
{
    private $actions;

    public function getAction($actionName)
    {
        return $this->actions[$actionName];
    }

    public function addAction($actionName, ActionInterface $actionInstance)
    {
        $this->$actions[$actionName] = $actionInstance;

        return $this;
    }
}