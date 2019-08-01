<?php
declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

class Help extends Base
{
    public function doAction(array $data): void
    {
        if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
            $PL = $this->bot->getPL();
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

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
                $this->error = $lang->bot_help_error;
            }

            $this->message = $command;
            
            $this->shout();
        }
    }
}
