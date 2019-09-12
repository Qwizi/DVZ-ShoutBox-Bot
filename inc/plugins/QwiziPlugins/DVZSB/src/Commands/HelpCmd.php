<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Command;
use Qwizi\DVZSB\Actions\Pagination;

class HelpCmd extends AbstractCommandBase
{
    public function doAction(array $data): void
    {
        if ($this->isMatched($data)) {
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

            $paginationCommandsArray = $pagination->paginate($commandsArray, (int) $this->getArgs()[0]);

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
