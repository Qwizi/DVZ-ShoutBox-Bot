<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Validators;

use \Qwizi\DVZSB\Bot;

abstract class Validator
{
    protected $error_messages = [];
    private $shoutData = [];
    private $validated = false;
    private $tag;

    public function setShoutData($shoutData) {
        $this->shoutData = $shoutData;
    }

    public function shoutErrorMsg() {
        $message = $this->error_messages[$this->tag];
        Bot::shout($message, $this->shoutData['uid'], $this->shoutData['shout_id']);
    }

    public function getValidateState(): bool {
        return $this->validated;
    }

    public function setValidateState(bool $state, string $tag=null) {
        $this->validated = $state;
        if (!$state) $this->tag = $tag;
    }

    abstract public function validate($argumentValue);
}