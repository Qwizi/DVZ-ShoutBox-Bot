<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Exceptions\ApplicationException;
use Qwizi\DVZSB\Exceptions\UserNotFoundException;

class SetBot extends Base
{
    public function doAction(array $data): void
    {
        if (!$this->bot->accessMod()) {
            return;
        }

        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            
            $db = $this->bot->getDB();
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            try {
                $user = $this->bot->getUserInfoFromUid($data['uid']);
                $target = $this->bot->getUserInfoFromUsername($matches[1]);

                if (empty($target)) {
                    throw new UserNotFoundException($lang->bot_ban_error_empty_user);
                }

                if (empty($user)) {
                    throw new UserNotFoundException($lang->bot_ban_error_empty_user);
                }

                $db->update_query('settings', ['value' => $db->escape_string((int) $target['uid'])], "name='dvz_sb_bot_id'");

                $this->bot->rebuildSettings();

            } catch (ApplicationException $e) {
                $this->error = $e->getMessage();
            }

            $lang->bot_setbot_message_success = $lang->sprintf($lang->bot_setbot_message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

            $this->message = $lang->bot_setbot_message_success;
            
            $this->shout();
        }
    }
}