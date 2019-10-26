<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use Qwizi\DVZSB\Actions\ActionInterface;

class CommandAction
{
    private $actions;

    public function getActions()
    {
        return $this->actions;
    }

    public function get($actionName)
    {
        return $this->actions[$actionName];
    }

    public function add($actionName, ActionInterface $actionInstance)
    {
        $this->actions[$actionName] = $actionInstance;

        return $this;
    }
}