<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use \Qwizi\DVZSB\Validators\Validator;

class IsBannedValidator extends Validator
{
    public function __construct() {
        $this->error_messages = [
            'user_not_banned' => 'User is not banned'
        ];
    }

    public function validate($argumentValue) {
        global $mybb, $db;
        $explodeBannedUsers = \explode(",", $mybb->settings['dvz_sb_blocked_users']);
        $argumentValue = (int) $argumentValue;
        if (!in_array($argumentValue, $explodeBannedUsers)) {
            $this->setValidateState(false, 'user_not_banned');
        } else {
            $this->setValidateState(true);
        }
    }
}