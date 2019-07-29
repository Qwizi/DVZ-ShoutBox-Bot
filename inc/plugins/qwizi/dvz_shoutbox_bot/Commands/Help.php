<?php

class Qwizi_DVZSB_Commands_Help extends Qwizi_DVZSB_Commands_Base
{
    public function doAction($data)
    {
        if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
            $PL = $this->bot->getPL();
            $commandPrefix = $this->bot->settings('commands_prefix');

            $pluginCache = $PL->cache_read('dvz_shoutbox_bot');
            $commandsArray = $pluginCache['commands'];

            if (!empty($commandsArray)) {
                $command = '';
                for ($i = 0; $i < count($commandsArray); $i++) {
                    // [quote="{username}" pid="{pid}" dateline="{dateline}"]{message}[/quote]
                    $command .= "{$commandPrefix}{$commandsArray[$i]['command']} - {$commandsArray[$i]['description']}\n";
                }
            } else {
                $this->error = "Wystąpił problem.";
            }

            $this->message = $command;

            $this->shout();
        }
    }
}
