<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

class SetBot extends Base
{
    public function doAction(array $data): void
    {
        global $lang; //TODO: Qwizi fix it
        if (!$this->bot->accessMod()) {
            return;
        }

        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $user = $this->bot->getUserInfoFromUid($data['uid']);
            $target = $this->bot->getUserInfoFromUsername($matches[1]);
            $db = $this->bot->getDB();

            if (empty($target)) {
                $this->error = $lang->bot_setbot_error_empty_user;
            } else {
                $db->update_query('settings', ['value' => $db->escape_string((int) $target['uid'])], "name='dvz_sb_bot_id'");
            }

            $this->bot->rebuildSettings();

            $this->message = "@\"{$user['username']}\"" . $lang->bot_setbot_message_success . "@\"{$target['username']}\"";
            $this->shout();
        }
    }
}
