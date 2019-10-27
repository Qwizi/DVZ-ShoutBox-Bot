<?php

namespace Qwizi\TestCommands\Commands;

use Qwizi\DVZSB\AbstractCommand;
use Qwizi\DVZSB\Commands\CommandInterface;

class TestCmd extends AbstractCommand implements CommandInterface
{
    public function handle()
    {
        $this->action->get('test')->execute('Hello');

        $this->bot->shout('test');
    }
}