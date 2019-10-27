<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use Qwizi\DVZSB\Validators\AbstractValidator;

class IsIntegerValidator extends AbstractValidator
{
    public function validate($target, $additional = null): bool
    {
        try {
            if (!is_int($target)) {
                throw new \Exception($this->get('lang')->integer);
            }
            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
}
