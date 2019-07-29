<?php

class Qwizi_DVZSB_Commands_Ban extends Qwizi_DVZSB_Commands_Base
{
    public function doAction($data)
    {
        if ($this->bot->accessMod()) {
            if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
                $target = $this->bot->getUserInfoFromUsername($matches[1]);
                $user = $this->bot->getUserInfoFromUid($data['uid']);
                $mybb = $this->bot->getMybb();
                $db = $this->bot->getDB();

                if (empty($target)) {
                    $this->error = "Nie znaleziono użytkownika";
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

                                $this->rebuildSettings();
                            } else {
                                $this->error = "Nie możesz ponownie zbanować tego uzytkownika";
                            }
                        }
                    } else {
                        $this->error = "Nie możesz sam siebie zbanować";
                    }
                }

                $this->message = "@\"{$user['username']}\" zbanował użytkownika @\"{$target['username']}\"";

                $this->shout();
            }
        }
    }
}
