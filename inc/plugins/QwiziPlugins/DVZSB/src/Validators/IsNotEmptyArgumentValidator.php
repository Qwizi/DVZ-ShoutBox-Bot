<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use Qwizi\DVZSB\Validators\AbstractValidator;

class IsNotEmptyArgumentValidator extends AbstractValidator
{
    public function validate($target, $additional = null): bool
    {
        try {
            if (!is_array($additional) || empty($additional)) {
                throw new \Exception('Additonal informations cannot be empty');
            }

            if (empty($target)) {
                $this->get('lang')->empty_arguments = $this->get('lang')->sprintf(
                    $this->get('lang')->empty_arguments,
                    $additional['prefix'].$additional['command'],
                    $additional['arguments']
                );
                throw new \Exception($this->get('lang')->empty_arguments);
            }

            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }
    }
}
