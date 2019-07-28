<?php

class Qwizi_DVZSB_Commands_BanList implements Qwizi_DVZSB_Commands_Base
{
    private $bot;

    public function __construct(Qwizi_DVZSB_Bot $bot)
    {
        $this->bot = $bot;
    }

    public function getBot()
    {
        return $this->bot;
    }

    public function banList()
    {
        $error = [];
        $mybb = $this->bot->getMybb();
        $db = $this->bot->getDB();
        $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

        if (in_array('', $explodeBannedUsers)) {
            $error['msg'] = "Brak zbanowanych użytkowników";
        }

        if (empty($error)) {
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
            return $this->bot->shout($error['msg']);
        }
    }

    public function doAction($data)
    {
        if ($this->bot->accessMod()) {
            if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
                // Banlist
                $this->banList();
            }
        }
    }
}
