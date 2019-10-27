<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use Qwizi\DVZSB\Validators\AbstractValidator;

class IsUserValidator extends AbstractValidator
{
    public function validate($target, $additional = null): bool
    {
        try {
            $validatedUser = get_user($target['uid']);

            if (empty($validatedUser)) {
                throw new \Exception($this->get('lang')->user_not_found);
            }

            return true;

        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }
    }
}
