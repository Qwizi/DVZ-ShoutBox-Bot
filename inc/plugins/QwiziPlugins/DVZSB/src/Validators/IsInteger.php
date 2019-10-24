<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use MyLanguage;
use Qwizi\DVZSB\Interfaces\ValidationInterface;

class IsInteger implements ValidationInterface
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

    public function validate($target, array $additional)
    {
        if (is_int($target)) {
            return true;
        }

        $this->setError($target. ' musi być liczbą całkowitą');

        return false;
    }
}
