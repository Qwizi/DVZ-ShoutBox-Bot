<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Interfaces\ModRequiredInterface;

class BanListCmd extends Base implements CommandInterface, ModRequiredInterface
{
    public function pattern(string $commandData): string
    {
        $command = $this->baseCommandPattern($commandData);

        $pattern = '(' . $command . ')';

        $ReturnedPattern = '/^' . $pattern . '$/';

        return $ReturnedPattern;
    }

    public function doAction(array $data): void
    {
        if (preg_match($this->pattern($data['command']), $data['text'], $matches)) {
            $this->lang->load('dvz_shoutbox_bot');

            $explodeBannedUsers = explode(",", $this->mybb->settings['dvz_sb_blocked_users']);

            if (in_array('', $explodeBannedUsers)) {
                $this->setError($this->lang->bot_banlist_empty_list);
            }

            if (!$this->getError()) {

                rebuild_settings();
                
                $usernamesArray = [];

                for ($i = 0; $i < count($explodeBannedUsers); $i++) {
                    array_push($usernamesArray, $this->getUserInfoFromId((int) $explodeBannedUsers[$i]));
                }

                foreach ($usernamesArray as $index) {
                    $usernames[] = "@\"{$index['username']}\"";
                }
                $implode = implode(", ", $usernames);

                $this->lang->bot_banlist_list_banned = $this->lang->sprintf($this->lang->bot_banlist_list_banned, $implode);

                $this->setMessage($this->lang->bot_banlist_list_banned);
            }

            $this->send()->setReturnedValue([
                'banned' => $implode,
                'message' => $this->getMessage(),
                'error' => $this->getError()
            ])->run_hook('dvz_shoutbox_bot_commands_banlist_commit');
        }
    }
}
