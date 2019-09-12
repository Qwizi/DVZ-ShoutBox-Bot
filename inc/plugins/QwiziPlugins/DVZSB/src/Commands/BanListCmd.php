<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\ModRequiredInterface;

class BanListCmd extends AbstractCommandBase implements  ModRequiredInterface
{
    public function doAction(array $data): void
    {
        if ($this->isMatched($data)) {
            $this->lang->load('dvz_shoutbox_bot_banlist');

            $explodeBannedUsers = explode(",", $this->mybb->settings['dvz_sb_blocked_users']);

            if (in_array('', $explodeBannedUsers)) {
                $this->setError($this->lang->empty_list);
            }

            if (!$this->getError()) {

                rebuild_settings();
                
                $usernamesArray = [];

                for ($i = 0; $i < count($explodeBannedUsers); $i++) {
                    array_push($usernamesArray, get_user((int) $explodeBannedUsers[$i]));
                }

                foreach ($usernamesArray as $index) {
                    $usernames[] = $this->mentionUsername($index['username']);
                }
                $implode = implode(", ", $usernames);

                $message_succcess = $this->lang->sprintf($this->lang->list_banned, $implode);

                $this->setMessage($message_succcess);
                $this->setReturnedValue([
                    'banned' => $implode,
                    'message' => $this->getMessage(),
                ]);
            } else {
                $this->setReturnedValue([
                    'error' => $this->getError()
                ]);
            }

            $this->send()->run_hook('dvz_shoutbox_bot_commands_banlist_commit');
        }
    }
}
