<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use \Qwizi\DVZSB\Bot;

abstract class Validator
{
    protected $error_messages = [];
    protected $shoutData = [];
    public function setShoutData($shoutData) {
        $this->shoutData = $shoutData;
    }

    protected function shoutErrorMsg($tag) {
        $message = $this->error_messages[$tag];
        Bot::shout($message, $this->shoutData['uid'], $this->shoutData['shout_id']);
    }
    abstract public function validate($argumentValue): bool;
}