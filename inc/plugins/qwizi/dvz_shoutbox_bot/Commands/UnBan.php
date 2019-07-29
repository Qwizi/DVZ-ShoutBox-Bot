<?php

class Qwizi_DVZSB_Commands_UnBan extends Qwizi_DVZSB_Commands_Base
{
    public function doAction($data)
    {
        if ($this->bot->accessMod()) {
            if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
                $user = $this->bot->getUserInfoFromUid($data['uid']);
                $target = $this->bot->getUserInfoFromUsername($matches[1]);

                $mybb = $this->bot->getMybb();
                $db = $this->bot->getDB();
                $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

                if (empty($target)) {
                    $this->error = "Nie znaleziono użytkownika";
                } else {
                    if ($target['uid'] != $mybb->user['uid']) {
                        if (in_array($target['uid'], $explodeBannedUsers)) {
                            if (($key = array_search($target['uid'], $explodeBannedUsers)) !== false) {
                                unset($explodeBannedUsers[$key]);
                            }
                            $implodeBannedUsers = implode(",", $explodeBannedUsers);
                            $db->update_query('settings', ['value' => $db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
                            $this->rebuildSettings();
                        } else {
                            $this->error = "Uzytkownik nie posiada bana";
                        }
                    } else {
                        $this->error = "Nie możesz sam siebie odbanować";
                    }
                }

                $this->message = "@\"{$user['username']}\" odbanował użytkownika @\"{$target['username']}\"";

                $this->shout();
            }
        }
    }
}
