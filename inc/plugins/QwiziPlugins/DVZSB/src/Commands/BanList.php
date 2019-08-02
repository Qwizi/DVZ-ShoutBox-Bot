<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Exceptions\ApplicationException;

class BanList extends Base
{
    public function doAction(array $data): void
    {
        if (!$this->bot->accessMod()) {
            return;
        }

        if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
            $mybb = $this->bot->getMybb();
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            try {
                $this->bot->rebuildSettings();
                
                $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

                if (in_array('', $explodeBannedUsers)) {
                    throw new ApplicationException($lang->bot_banlist_empty_list);
                }

                $usernamesArray = [];

                for ($i = 0; $i < count($explodeBannedUsers); $i++) {
                    array_push($usernamesArray, $this->bot->getUserInfoFromUid((int) $explodeBannedUsers[$i]));
                }

                foreach ($usernamesArray as $index) {
                    $usernames[] = "@\"{$index['username']}\"";
                }
                $implode = implode(", ", $usernames);

            } catch (ApplicationException $e) {
                $this->error = $e->getMessage();
            }

            $lang->bot_banlist_list_banned = $lang->sprintf($lang->bot_banlist_list_banned, $implode);

            $this->message = $lang->bot_banlist_list_banned;

            $this->shout();
        }
    }

}