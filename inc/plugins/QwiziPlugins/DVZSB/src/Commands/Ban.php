<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Exceptions\ApplicationException;
use Qwizi\DVZSB\Exceptions\UserNotFoundException;
use Qwizi\DVZSB\Exceptions\CannotActionMyselfException;

class Ban extends Base
{
    public function doAction(array $data): void
    {
        if (!$this->bot->accessMod()) {
            return;
        }

        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $mybb = $this->bot->getMybb();
            $db = $this->bot->getDB();
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            try {
                $target = $this->bot->getUserInfoFromUsername($matches[1]);
                $user = $this->bot->getUserInfoFromUid((int) $data['uid']);
                $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);

                if (empty($target)) {
                    throw new UserNotFoundException($lang->bot_ban_error_empty_user);
                }

                if (empty($user)) {
                    throw new UserNotFoundException($lang->bot_ban_error_empty_user);
                }

                if ($target['uid'] == $mybb->user['uid']) {
                    throw new CannotActionMyselfException($lang->bot_ban_error_ban_myself);
                }

                if (in_array($target['uid'], $explodeBannedUsers)) {
                    throw new ApplicationException($lang->bot_ban_error_multiban_user);
                }

                if (in_array('', $explodeBannedUsers)) {
                    $db->update_query('settings', ['value' => $db->escape_string((int) $target['uid'])], "name='dvz_sb_blocked_users'");
                } else {
                    array_push($explodeBannedUsers, $target['uid']);
                    $implodeBannedUsers = implode(",", $explodeBannedUsers);

                    $db->update_query('settings', ['value' => $db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
                }

                $this->bot->rebuildSettings();
                
            } catch (ApplicationException $e) {
                $this->error = $e->getMessage();
            }

            $lang->bot_ban_message_success = $lang->sprintf($lang->bot_ban_message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

            $this->message = $lang->bot_ban_message_success;

            $this->shout();
        }
    }
}