<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Exceptions\ApplicationException;

class BanListCmd extends Base implements CommandInterface
{
    public function pattern(string $commandData): string
    {
        /* $pattern = '/^\\' . $this->bot->settings('commands_prefix') . preg_quote($command) . '$/'; */

        $command = $this->baseCommandPattern($commandData);

        $pattern = '(' . $command . ')';

        $ReturnedPattern = '/^' . $pattern . '$/';

        return $ReturnedPattern;
    }
    
    public function doAction(array $data): void
    {
        if (!$this->accessMod()) {
            return;
        }
        // $data['text'] == $this->bot->settings('commands_prefix') . $data['command']
        if (preg_match($this->pattern($data['command']), $data['text'], $matches)) {
            $this->lang->load('dvz_shoutbox_bot');

            try {
                $this->rebuildSettings();
                
                $explodeBannedUsers = explode(",", $this->mybb->settings['dvz_sb_blocked_users']);

                if (in_array('', $explodeBannedUsers)) {
                    throw new ApplicationException($this->lang->bot_banlist_empty_list);
                }

                $usernamesArray = [];

                for ($i = 0; $i < count($explodeBannedUsers); $i++) {
                    array_push($usernamesArray, $this->getUserNameFromId((int) $explodeBannedUsers[$i]));
                }

                foreach ($usernamesArray as $index) {
                    $usernames[] = "@\"{$index['username']}\"";
                }
                $implode = implode(", ", $usernames);

            } catch (ApplicationException $e) {
                $this->setError($e->getMessage());
            }

            $this->lang->bot_banlist_list_banned = $this->lang->sprintf($this->lang->bot_banlist_list_banned, $implode);

            $this->setMessage($this->lang->bot_banlist_list_banned);

            $this->send();

            $this->returned_value = [
                'banned' => $implode,
                'message' => $this->getMessage(),
                'error' => $this->getError()
            ];

            $this->plugins->run_hooks("dvz_shoutbox_bot_commands_banlist_commit", $this->returned_value);
        }
    }

}