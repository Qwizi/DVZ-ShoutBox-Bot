<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use Qwizi\DVZSB\Validators\AbstractValidator;

class IsSuperAdminValidator extends AbstractValidator
{
    public function validate($target, $additional = null): bool
    {
        try {
            if (!is_super_admin((int)$target)) {
                throw new \Exception($this->get('lang')->is_super_admin);
            }
            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }
    }
}
