<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use MyLanguage;
use Qwizi\DVZSB\Validators\ValidatorInterface;

class IsNotEmptyArgumentValidator implements ValidatorInterface
{
    private $error;

    private $lang;

    public function __construct(MyLanguage $lang)
    {
        $this->lang = $lang;
    }

    /**
     * Get the value of error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the value of error
     *
     * @return  self
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    public function validate($target, $additional)
    {
        if (isset($this->error)) {
            unset($this->error);
        }

        if (!empty($target) && is_array($target)) {
            return true;
        }

        $this->lang->empty_arguments = $this->lang->sprintf(
            $this->lang->empty_arguments,
            $additional['prefix'].$additional['command'],
            $additional['arguments']
        );

        $this->setError($this->lang->empty_arguments);

        return false;
    }
}
