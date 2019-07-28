<?php

class Qwizi_DVZSB_Commands_UnBan implements Qwizi_DVZSB_Commands_Base
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

    public function unBanUser($user, $target)
    {
        $error = [];
        $mybb = $this->bot->getMybb();
        $db = $this->bot->getDB();
        $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

        if (empty($target)) {
            $error['msg'] = "Nie znaleziono użytkownika";
        } else {
            if ($target['uid'] != $mybb->user['uid']) {
                if (in_array($target['uid'], $explodeBannedUsers)) {
                    if (($key = array_search($target['uid'], $explodeBannedUsers)) !== false) {
                        unset($explodeBannedUsers[$key]);
                    }
                    $implodeBannedUsers = implode(",", $explodeBannedUsers);
                    $db->update_query('settings', ['value' => $db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
                } else {
                    $error['msg'] = "Uzytkownik nie posiada bana";
                }
            } else {
                $error['msg'] = "Nie możesz sam siebie odbanować";
            }
        }

        if (empty($error)) {
            $this->bot->shout("@\"{$user['username']}\" odbanował użytkownika @\"{$target['username']}\"");
            $this->bot->rebuildSettings();
        } else {
            return $this->bot->shout($error['msg']);
        }
    }

    public function doAction($data)
    {
        if ($this->bot->accessMod()) {
            if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
                $user = $this->bot->getUserInfoFromUid($data['uid']);
                $target = $this->bot->getUserInfoFromUsername($matches[1]);

                // Ban user
                $this->unBanUser($user, $target);
            }
        }
    }
}
