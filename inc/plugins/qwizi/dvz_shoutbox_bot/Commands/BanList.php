<?php

class Qwizi_DVZSB_Commands_BanList implements Qwizi_DVZSB_Commands_Base
{
    private $bot;

    public function __construct(Qwizi_DVZSB_Bot $bot)
    {
        $this->bot = $bot;
    }

    public function banList()
    {
        $errMsg = '';
        $mybb = $this->bot->getMybb();
        $db = $this->bot->getDB();
        $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

        if (in_array('', $explodeBannedUsers)) {
            $errMsg = "Brak zbanowanych użytkowników";
        }

        if ($errMsg == '') {
            $usernamesArray = [];
            
            for ($i = 0; $i < count($explodeBannedUsers); $i++) {
                array_push($usernamesArray, $this->bot->getUserInfoFromUid($explodeBannedUsers[$i]));
            }

            foreach ($usernamesArray as $index) {
                $usernames[] = "@\"{$index['username']}\"";
            }
            $implode = implode(", ", $usernames);

            $this->bot->shout("Zbanowani: {$implode}");
        } else {
            return $this->bot->shout($errMsg);
        }
    }

    public function doAction($text, $uid)
    {
        if ($this->bot->accessMod()) {
            if ($text == $this->bot->settings('commands_prefix') . 'banlist') {
                // Banlist
                $this->banList();
            }
        }
    }
}
