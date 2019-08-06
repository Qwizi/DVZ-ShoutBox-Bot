<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;

use Qwizi\DVZSB\Pagination;

class HelpCmd extends Base implements CommandInterface
{
    public function pattern(string $commandData): string
    {
        /* $pattern = '/^\\' . $this->bot->settings('commands_prefix') . preg_quote($command) . '$/'; */

        $command = $this->baseCommandPattern($commandData);

        $pattern = '(' . $command . '|' . $command . '[\s]([0-9]+))';

        $ReturnedPattern = '/^' . $pattern . '$/';

        return $ReturnedPattern;
    }

    public function doAction(array $data): void
    {
        if (preg_match($this->pattern($data['command']), $data['text'], $matches)) {

            $this->lang->load('dvz_shoutbox_bot');

            $commandPrefix = $this->getCommandPrefix();

            $pluginCache = $this->PL->cache_read('dvz_shoutbox_bot');
            $commandsArray = $pluginCache['commands'];

            if (empty($commandsArray)) {
                $this->setError($this->lang->bot_help_error);
            }

            $pagination = new Pagination;

            $paginationCommandsArray = $pagination->paginate($commandsArray, (int) $matches[2]);

            if (empty($paginationCommandsArray)) {
                $this->setError($this->lang->bot_help_error);
            }
            
            if (!$this->getError()) {
                $command = '';
                for ($i = 0; $i < count($paginationCommandsArray); $i++) {
                    // [quote="{username}" pid="{pid}" dateline="{dateline}"]{message}[/quote]
                    $command .= "{$commandPrefix}{$paginationCommandsArray[$i]['command']} - {$paginationCommandsArray[$i]['description']}\n";
                }

                $this->setMessage($command);
            }

            $this->send()->setReturnedValue([
                'message' => $this->getMessage(),
                'error' => $this->getError(),
            ])->run_hook('dvz_shoutbox_bot_commands_help_commit');
        }
    }
}
