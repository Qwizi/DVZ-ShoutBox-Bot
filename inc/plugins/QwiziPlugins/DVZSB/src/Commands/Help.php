<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Exceptions\ApplicationException;

class Help extends Base
{
    public function doAction(array $data): void
    {
        if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
            $PL = $this->bot->getPL();
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            try {
                $commandPrefix = $this->bot->settings('commands_prefix');

                $pluginCache = $PL->cache_read('dvz_shoutbox_bot');
                $commandsArray = $pluginCache['commands'];

                if (empty($commandsArray)) {
                    throw new ApplicationException($lang->bot_help_error);
                }

                $command = '';
                for ($i = 0; $i < count($commandsArray); $i++) {
                    // [quote="{username}" pid="{pid}" dateline="{dateline}"]{message}[/quote]
                    $command .= "{$commandPrefix}{$commandsArray[$i]['command']} - {$commandsArray[$i]['description']}\n";
                }

            } catch (ApplicationException $e) {
                $this->error = $e->getMessage();
            }

            $this->message = $command;

            $this->shout();
        }
    }
}
