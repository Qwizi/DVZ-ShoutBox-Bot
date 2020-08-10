<?php

declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use \Qwizi\DVZSB\Commands\Command;

use \Qwizi\DVZSB\CommandManager;
use \Qwizi\DVZSB\Bot;

use \Qwizi\DVZSB\Actions\PMAction;

class HelpCmd extends Command
{
    public function __construct($shoutData, $commandData) {
        parent::__construct($shoutData, $commandData);
    }
    public function handle() {
        global $mybb;
        $commands = [];
        $commands = CommandManager::getCommands();

        $msg = '';
        $msg .= "Available Commands: [list]";

        foreach($commands as $command) {
            if (class_exists($command['namespace'])) {
                $command['prefix'] = $command['prefix'] = $mybb->settings['dvz_sb_bot_commands_prefix'];

                $commandInstance = new $command['namespace']($this->shoutData, $command);
                $hint = $commandInstance->getHint();
                $hint = str_replace('Usage ', '', $hint);
                $msg .= sprintf("[*] %s \n[%s]\n", $hint, $command['description']);
            }
        }
        $msg .= "[/list] ";
        //echo $message;
        //exit;

        PMAction::send('Bot - List of Commands', $msg, $this->shoutData['uid']);

        Bot::shout('Available commands list send via pm to you', $this->shoutData['uid'], $this->shoutData['shout_id']);
    }

}