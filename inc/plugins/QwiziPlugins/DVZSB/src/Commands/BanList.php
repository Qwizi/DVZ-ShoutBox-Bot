<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

class BanList extends Base
{
    public function doAction(array $data): void
    {
        global $lang;
        if (!$this->bot->accessMod()) {
            return;
        }

        if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
            $mybb = $this->bot->getMybb();
            $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

            if (in_array('', $explodeBannedUsers)) {
                $this->error = $lang->bot_banlist_empty_list;
            } else {
                $usernamesArray = [];

                for ($i = 0; $i < count($explodeBannedUsers); $i++) {
                    array_push($usernamesArray, $this->bot->getUserInfoFromUid((int) $explodeBannedUsers[$i]));
                }

                foreach ($usernamesArray as $index) {
                    $usernames[] = "@\"{$index['username']}\"";
                }
                $implode = implode(", ", $usernames);
            }

            $this->message = $lang->bot_banlist_list_banned . "{$implode}";
            $this->shout();
        }
    }

}
