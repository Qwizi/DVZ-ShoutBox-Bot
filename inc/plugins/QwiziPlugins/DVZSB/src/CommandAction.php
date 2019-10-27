<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use Qwizi\DVZSB\Actions\ActionInterface;

class CommandAction
{
    /** @var array */
    private $actions = [];

    /**
     * Get actions
     * 
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get action
     * 
     * @return array
     */
    public function get(string $actionName)
    {
        return $this->actions[$actionName];
    }

    /**
     * Add action
     * 
     * @param string $actionName
     * @param AbstractAction $actionInstance
     */
    public function add(string $actionName, ActionInterface $actionInstance)
    {
        $this->actions[$actionName] = $actionInstance;

        return $this;
    }
}