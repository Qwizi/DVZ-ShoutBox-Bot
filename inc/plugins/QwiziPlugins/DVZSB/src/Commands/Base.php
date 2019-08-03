<?php
declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Bot;

abstract class Base
{
    public $bot;
    public $error;
    public $message;
    public $returned_value = [];
    public $commandPrefix;

    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
        $this->commandPrefix = $this->bot->settings('commands_prefix');
    }

    public function getBot()
    {
        return $this->bot;
    }

    public function getCommandPrefix()
    {
        return $this->commandPrefix;
    }

    public function shout()
    {
        if (!$this->error) {
            $this->bot->shout($this->message);
        } else {
            $this->bot->shout($this->error);
        }
    }

    public function baseCommandPattern(string $command): string
    {
        $pattern = "\\".$this->getCommandPrefix().preg_quote($command);
        return $pattern;
    }
    
    abstract protected function pattern(string $commandData): string;
    abstract protected function doAction(array $data): void;
}