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

    public function validate($argumentValue) {
        $argumentValue = (int)$argumentValue;
        if (!is_super_admin($argumentValue)) {
            $this->setValidateState(false, 'invalid_user');
        } else {
            $this->setValidateState(true);
        }
    }
}
