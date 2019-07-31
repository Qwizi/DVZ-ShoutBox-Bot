<?php
declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Bot;

abstract class Base
{
    public $bot;
    public $error;
    public $message;

    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }

    public function getBot()
    {
        return $this->bot;
    }

    public function shout()
    {
        if (!$this->error) {
            $this->bot->shout($this->message);
        } else {
            $this->bot->shout($this->error);
        }
    }

    abstract protected function doAction(array $data): void;
}