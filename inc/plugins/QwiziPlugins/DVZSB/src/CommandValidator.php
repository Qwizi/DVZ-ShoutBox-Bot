<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use Qwizi\DVZSB\Interfaces\ValidationInterface;

class CommandValidator
{
    private $validations = [];
    
    public function getValidation($validationName)
    {
        return $this->validations[$validationName];
    }

    public function addValidation($validationName, ValidationInterface $validationInstance)
    {
        if (!get_class($validationInstance)) {
            throw('Class '. $validationInstance. ' not exits');
        }
        $this->validations[$validationName] = $validationInstance;

        return $this;
    }

    public function getErrors()
    {
        $errors = [];
        foreach ($this->validations as $validation) {
            if (!empty($validation->getError())) {
                $errors[$validation] = $validation->getError();
            }
        }
        return $errors;
    }

    public function isValidated() {
        return empty($this->getErrors()) ? true : false;
    }
}
