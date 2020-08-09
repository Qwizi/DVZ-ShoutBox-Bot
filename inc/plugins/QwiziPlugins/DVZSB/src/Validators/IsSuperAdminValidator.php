<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use Qwizi\DVZSB\Validators\Validator;

class IsSuperAdminValidator extends Validator
{
    public function __construct() {
        $this->error_messages = [
            'invalid_user' => 'Invalid user '
        ];
    }

    public function validate($argumentValue): bool {
        $argumentValue = (int)$argumentValue;
        if (is_super_admin($argumentValue)) {
            $this->shoutErrorMsg('invalid_user');
            return false;
        }
        return true;
    }
}
