<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use Qwizi\DVZSB\Validators\ValidatorInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    /** @var string */
    protected $error;

    /** @var array */
    protected $variables = [];

    /**
     * Get the value of error
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Set the value of error
     *
     * @return  self
     */
    public function setError(string $error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get variable
     * 
     * @param string $variableName
     * 
     * @return array
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