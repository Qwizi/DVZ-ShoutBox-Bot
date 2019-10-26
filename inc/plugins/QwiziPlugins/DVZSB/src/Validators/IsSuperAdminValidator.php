<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use MyLanguage;
use Qwizi\DVZSB\Validators\ValidatorInterface;

class IsSuperAdminValidator implements ValidatorInterface
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
        if (is_super_admin($target['uid'])) {
            return true;
        }
        
        $this->setError($this->lang->target_is_super_admin);

        return false;
    }
}