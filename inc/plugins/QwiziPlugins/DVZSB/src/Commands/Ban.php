<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

class Ban extends Base
{
    public function doAction(array $data): void
    {
        if (!$this->bot->accessMod()) {
            return;
        }

        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $target = $this->bot->getUserInfoFromUsername($matches[1]);
            $user = $this->bot->getUserInfoFromUid((int) $data['uid']);
            $mybb = $this->bot->getMybb();
            $db = $this->bot->getDB();

            if (empty($target)) {
                $this->error = "Nie znaleziono użytkownika";
            } else {
                $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

                if ($target['uid'] != $mybb->user['uid']) {
                    if (in_array('', $explodeBannedUsers)) {
                        $db->update_query('settings', ['value' => $db->escape_string((int) $target['uid'])], "name='dvz_sb_blocked_users'");
                    } else {
                        if (!in_array($target['uid'], $explodeBannedUsers)) {
                            array_push($explodeBannedUsers, $target['uid']);
                            $implodeBannedUsers = implode(",", $explodeBannedUsers);

                            $db->update_query('settings', ['value' => $db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
                        } else {
                            $this->error = "Nie możesz ponownie zbanować tego uzytkownika";
                        }
                    }
                } else {
                    $this->error = "Nie możesz sam siebie zbanować";
                }
            }

            $this->bot->rebuildSettings();

            $this->message = "@\"{$user['username']}\" zbanował użytkownika @\"{$target['username']}\"";

            $this->shout();
        }
    }
}
