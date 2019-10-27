<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use MyLanguage;
use Qwizi\DVZSB\Validators\ValidatorInterface;

class CommandValidator
{
    /** @var array */
    private $errors = [];

    /** @var array */
    private $validators = [];
    
    /** @var MyLanguage */
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
    
    /**
     * Get validator
     * 
     * @return array
     */
    public function get($validatorName)
    {
        return $this->validators[$validatorName];
    }

    /**
     * Add validator
     * 
     * @param string $validatorName
     */
    public function add(string $validatorName, ValidatorInterface $validatorInstance)
    {
        $this->validators[$validatorName] = $validatorInstance;

        return $this;
    }

    /**
     * Get validators errors
     * 
     * @return array
     */
    public function getErrors()
    {
        foreach ($this->validators as $key => $validator) {
            if (!empty($validator->getError())) {
                $this->errors[$key] = $validator->getError();
            }
        }

        return $this->errors;
    }


    /**
     * Check is validated
     * 
     * @return bool
     */
    public function isValidated()
    {
        return empty($this->getErrors()) ? true : false;
    }
}
