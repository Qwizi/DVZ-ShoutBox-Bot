<?php

class Qwizi_DVZSB_Commands_Ban implements Qwizi_DVZSB_Commands_Base
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

    public function banUser($user, $target)
    {
        $error = [];
        $mybb = $this->bot->getMybb();
        $db = $this->bot->getDB();

        if (empty($target)) {
            $error['msg'] = "Nie znaleziono użytkownika";
        } else {
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
                        $error['msg'] = "Nie możesz ponownie zbanować tego uzytkownika";
                    }
                }
            } else {
                $error['msg'] = "Nie możesz sam siebie zbanować";
            }
        }

        if (empty($error)) {
            $this->bot->shout("@\"{$user['username']}\" zbanował użytkownika @\"{$target['username']}\"");
            $this->bot->rebuildSettings();
        } else {
            $this->bot->shout($error['msg']);
        }
    }

    public function doAction($data)
    {
        if ($this->bot->accessMod()) {
            if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
                $target = $this->bot->getUserInfoFromUsername($matches[1]);
                $user = $this->bot->getUserInfoFromUid($data['uid']);
                
                // Ban user
                $this->banUser($user, $target);
            }
        }
    }
}
