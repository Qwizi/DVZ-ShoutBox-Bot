<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Exceptions\ApplicationException;
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

            try {
                $commandPrefix = $this->getCommandPrefix();

                $pluginCache = $this->PL->cache_read('dvz_shoutbox_bot');
                $commandsArray = $pluginCache['commands'];

                if (empty($commandsArray)) {
                    throw new ApplicationException($this->lang->bot_help_error);
                }

                $pagination = new Pagination;

                $paginationCommandsArray = $pagination->paginate($commandsArray, (int)$matches[2]);

                if (empty($paginationCommandsArray)) {
                    throw new ApplicationException($this->lang->bot_help_error);
                }

                $command = '';
                for ($i = 0; $i < count($paginationCommandsArray); $i++) {
                    // [quote="{username}" pid="{pid}" dateline="{dateline}"]{message}[/quote]
                    $command .= "{$commandPrefix}{$paginationCommandsArray[$i]['command']} - {$paginationCommandsArray[$i]['description']}\n";
                }

            } catch (ApplicationException $e) {
                $this->setError($e->getMessage());
            }

            if ($command) {
                $this->setMessage($command);

            }
            
            $this->send();

            $this->returned_value = [
                'message' => $this->getMessage(),
                'error' => $this->getError(),
            ];

            $this->plugins->run_hooks("dvz_shoutbox_bot_commands_help_commit", $this->returned_value);
        }
    }
}
