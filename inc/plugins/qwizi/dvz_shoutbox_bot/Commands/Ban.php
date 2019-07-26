<?php

class Qwizi_DVZSB_Commands_Ban implements Qwizi_DVZSB_Commands_Base
{
    private $bot;

    public function __construct(Qwizi_DVZSB_Bot $bot)
    {
        $this->bot = $bot;
    }

    public function banUser($user, $target)
    {
        $errMsg = '';
        $mybb = $this->bot->getMybb();
        $db = $this->bot->getDB();
        $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

        if ($target['uid'] != $mybb->user['uid']) {
            if (in_array('', $explodeBannedUsers)) {
                $db->update_query('settings', ['value' => $db->escape_string($target['uid'])], "name='dvz_sb_blocked_users'");
            } else {
                if (!in_array($target['uid'], $explodeBannedUsers)) {
                    array_push($explodeBannedUsers, $target['uid']);
                    $implodeBannedUsers = implode(",", $explodeBannedUsers);
                    $db->update_query('settings', ['value' => $db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
                } else {
                    $errMsg = "Nie możesz ponownie zbanować tego uzytkownika";
                }
            }
        } else {
            $errMsg = "Nie możesz sam siebie zbanować";
        }

        if ($errMsg == '') {
            $this->bot->shout("@\"{$user['username']}\" zbanował użytkownika @\"{$target['username']}\"");
        } else {
            return $this->bot->shout($errMsg);
        }
    }

    public function doAction($text, $uid)
    {
        if ($this->bot->accessMod()) {
            if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote('ban') . '[\s]+(.*)$/', $text, $matches)) {
                $user = $this->bot->getUserInfoFromUid($uid);
                $target = $this->bot->getUserInfoFromUsername($matches[1]);

                // Ban user
                $this->banUser($user, $target);
            }
        }
    }
}
