<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use \Qwizi\DVZSB\Bot;
use \Qwizi\DVZSB\Commands\Command;
use \Qwizi\DVZSB\Actions\BanAction;
use \Qwizi\DVZSB\Validators\IsUserValidator;
use \Qwizi\DVZSB\Validators\IsNotBannedValidator;

class BanCmd extends Command
{
    public function __construct($shoutData, $commandData) {
        parent::__construct($shoutData, $commandData);
        $this->addArgument('target', 'int', [
            'validators' => [
                new IsUserValidator, 
                new IsNotBannedValidator
            ],
            'aliases' => ['t', 'user']
        ]);
    }

    public function handle()
    {
        $args = $this->parseArguments($this->shoutData['text']);
        $target = $args[0];
        if ($target['validated']) {
            BanAction::ban($target['value']);
            Bot::shout("Pomyślnie zbanowano użytkownika", $this->shoutData['uid'], $this->shoutData['shout_id']);
        }
        
        //BanAction::ban($target['value']);
        //Bot::shout('Zbanowano', $this->shoutData['uid'], $this->shoutData['shout_id']);
        /*
        $argumentValidation = $this->validator->get('not_empty_argument');

        $additional = [
            'prefix' => $this->commandData['prefix'],
            'command' => $this->commandData['command'],
            'arguments' => "<username>"
        ];

        if ($argumentValidation->validate($args, $additional)) {
            $userValidation = $this->validator->get('user');

            $user = get_user($this->shoutData['uid']);
            $target = get_user_by_username(trim($args[0]), ['fields' => 'uid, username']);

            $userValidation->validate($user['uid']);
            $userValidation->validate($target['uid']);
        }

        if ($this->validator->isValidated()) {
            $this->action->get('ban')->execute($target);

            $message = $this->lang->sprintf(
                $this->lang->success,
                $this->action->get('mention')->execute($user['username']),
                $this->action->get('mention')->execute($target['username'])
            );

            $log = $this->action->get('log')->execute(
                $this->lang->sprintf(
                    $this->lang->success,
                    $user['username'],
                    $target['username']
                )
            );

        } else {
            foreach ($this->validator->getErrors() as $error) {
                $message = $error;
            }
        }

        // Send message
        $this->bot->shout($message);
        */
    }
}
