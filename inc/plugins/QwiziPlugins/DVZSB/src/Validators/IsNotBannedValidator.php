<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use \Qwizi\DVZSB\Validators\Validator;

class IsNotBannedValidator extends Validator
{
    public function __construct() {
        $this->error_messages = [
            'user_banned' => 'User is already banned '
        ];
    }

    public function validate($argumentValue) {
        global $mybb, $db;
        $explodeBannedUsers = \explode(",", $mybb->settings['dvz_sb_blocked_users']);
        $argumentValue = (int) $argumentValue;
        if (in_array($argumentValue, $explodeBannedUsers)) {
            $this->setValidateState(false, 'user_banned');
        } else {
            $this->setValidateState(true);
        }
    }
}