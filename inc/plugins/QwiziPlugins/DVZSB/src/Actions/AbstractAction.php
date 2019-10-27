<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

use Qwizi\DVZSB\Actions\ActionInterface;

abstract class AbstractAction implements ActionInterface
{
    /** @var array */
    protected $variables = [];

    /**
     * Get variable
     * 
     * @param string $variableName Variable name
     * 
     * @return string
     */
    public function get(string $variableName)
    {
        return $this->variables[$variableName];
    }

    /**
     * Add variable
     * 
     * @param string $variableName
     * @param any $variableInstance
     */
    public function add(string $variableName, $variableInstance)
    {
        $this->variables[$variableName] = $variableInstance;
    }
}