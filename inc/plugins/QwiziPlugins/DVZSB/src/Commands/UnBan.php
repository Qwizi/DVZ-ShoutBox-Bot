<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

class UnBan extends Base
{
    public function doAction(array $data): void
    {
        if (!$this->bot->accessMod()) {
            return;
        }

        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $user = $this->bot->getUserInfoFromUid($data['uid']);
            $target = $this->bot->getUserInfoFromUsername($matches[1]);

            $mybb = $this->bot->getMybb();
            $db = $this->bot->getDB();
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

            if (empty($target)) {
                $this->error = $lang->bot_unban_empty_user;
            } else {
                if ($target['uid'] != $mybb->user['uid']) {
                    if (in_array($target['uid'], $explodeBannedUsers)) {
                        if (($key = array_search($target['uid'], $explodeBannedUsers)) !== false) {
                            unset($explodeBannedUsers[$key]);
                        }
                        $implodeBannedUsers = implode(",", $explodeBannedUsers);
                        $db->update_query('settings', ['value' => $db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
                    } else {
                        $this->error = $lang->bot_unban_no_ban;
                    }
                } else {
                    $this->error = $lang->bot_unban_error_unban_myself;
                }
            }

            $this->bot->rebuildSettings();
            
            $lang->bot_unban_message_success = $lang->sprintf($lang->bot_unban_message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

            $this->message = $lang->bot_unban_message_success;
            $this->shout();
        }
    }
}
