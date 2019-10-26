<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use Qwizi\DVZSB\Validators\ValidatorInterface;
use MyLanguage;

class CommandValidator
{
    private $errors = [];

    private $validations = [];
    
    private $lang;

    public function __construct(MyLanguage $lang)
    {
        $this->lang = $lang;
        $this->lang->load('dvz_shoutbox_bot_errors');
    }

    /**
     * Get the value of lang
     */ 
    public function getLang()
    {
        return $this->lang;
    }
    
    public function get($validationName)
    {
        return $this->validations[$validationName];
    }

    public function add($validationName, ValidatorInterface $validationInstance)
    {
        if (!get_class($validationInstance)) {
            throw('Class '. $validationInstance. ' not exits');
        }
        $this->validations[$validationName] = $validationInstance;

        return $this;
    }

    public function getErrors()
    {
        unset($this->errors);

        foreach ($this->validations as $key => $validation) {
            if (!empty($validation->getError())) {
                $this->errors[$key] = $validation->getError();
            }
        }

        return $this->errors;
    }

    public function isValidated()
    {
        return empty($this->getErrors()) ? true : false;
    }
}
