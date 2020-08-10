<?php

declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use \Qwizi\DVZSB\Commands\Command;
use \Qwizi\DVZSB\Bot;
use \Qwizi\DVZSB\Message;
use \Qwizi\DVZSB\Validators\IsUserValidator;
use \Qwizi\DVZSB\Validators\IsBannedValidator;
use \Qwizi\DVZSB\Actions\UnBanAction;

class UnBanCmd extends Command
{
    public function __construct($shoutData, $commandData) {
        parent::__construct($shoutData, $commandData);
        $this->addArgument('target', 'int', [
            'validators' => [
                new IsUserValidator,
                new IsBannedValidator
            ],
            'aliases' => ['t', 'user'],
            'is_required' => true
        ]);
    }

    public function handle() {
        $args = $this->parseArguments($this->shoutData['text']);
        $target = $args[0];
        if ($target['validated']) {
            UnBanAction::unban($target['value']);

            $targetData = \get_user($target['value']);
            $message = \sprintf("Successfully unbanned user %s ", Message::mentionUser($targetData['username'], (int)$targetData['uid']));

            Bot::shout($message, $this->shoutData['uid'], $this->shoutData['shout_id']);
        }
    }
}