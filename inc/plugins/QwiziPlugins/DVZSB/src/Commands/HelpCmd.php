<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Command;
use Qwizi\DVZSB\Pagination;

class HelpCmd extends AbstractCommandBase
{
    private $pattern = "/^({command}|{command}[\s]([0-9]+))$/";

    public function doAction(array $data): void
    {
        if (preg_match($this->createPattern($data['command'], $this->pattern), $data['text'], $matches)) {
            global $cache;

            $this->lang->load('dvz_shoutbox_bot_help');

            Command::createInstance($cache, $this->db);

            $commandPrefix = $this->getCommandPrefix();
            
            $commandsArray = Command::i()->getCommands();

            if (empty($commandsArray)) {
                $this->setError($this->error);
            }

            $pagination = new Pagination;
            $pagination->setPerPage(9);

            $paginationCommandsArray = $pagination->paginate($commandsArray, (int) $matches[2]);

            if (empty($paginationCommandsArray)) {
                $this->setError($this->lang->error);
            }

            if (!$this->getError()) {

                $command = '';
                foreach ($paginationCommandsArray as $key => $value) {
                    $command .= "{$commandPrefix}{$value['command']} - {$value['description']} \r\n";
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
