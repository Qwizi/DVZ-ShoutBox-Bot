<?php

declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use \Qwizi\DVZSB\Actions\BanAction;
use \Qwizi\DVZSB\Bot;
use \Qwizi\DVZSB\Message;
use \Qwizi\DVZSB\Commands\Command;
use \Qwizi\DVZSB\Validators\IsNotBannedValidator;
use \Qwizi\DVZSB\Validators\IsUserValidator;

class BanCmd extends Command
{
    public function __construct($shoutData, $commandData)
    {
        parent::__construct($shoutData, $commandData);
        $this->addArgument('target', 'int', [
            'validators' => [
                new IsUserValidator,
                new IsNotBannedValidator,
            ],
            'aliases' => ['t', 'user'],
        ]);
    }

    public function handle()
    {
        $args = $this->parseArguments($this->shoutData['text']);
        $target = $args[0];
        if ($target['validated']) {
            BanAction::ban($target['value']);

            $targetData = \get_user($target['value']);
            $message = \sprintf("Successfully banned user %s ", Message::mentionUser($targetData['username'], (int)$targetData['uid']));

            Bot::shout($message, $this->shoutData['uid'], $this->shoutData['shout_id']);
        }
    }
}
