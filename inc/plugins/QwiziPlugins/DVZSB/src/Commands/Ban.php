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
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            if (empty($target)) {
                $this->error = $lang->bot_ban_error_empty_user;
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
                            $this->error = $lang->bot_ban_error_multiban_user;
                        }
                    }
                } else {
                    $this->error = $lang->bot_ban_error_ban_myself;
                }
            }

            $this->bot->rebuildSettings();

            $lang->bot_ban_message_success = $lang->sprintf($lang->bot_ban_message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

            $this->message = $lang->bot_ban_message_success;
            
            $this->shout();
        }
    }
}
