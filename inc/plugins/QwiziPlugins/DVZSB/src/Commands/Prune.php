<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

class Prune extends Base
{
    public function doAction(array $data): void
    {
        global $lang;
        if (!$this->bot->accessMod()) {
            return;
        }

        if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
            $this->bot->delete();
            $this->message = $lang->bot_prune_all_message;
            $this->shout();
        }

        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $user = $this->bot->getUserInfoFromUid($data['uid']);
            $target = $this->bot->getUserInfoFromUsername($matches[1]);

            $this->bot->delete("id={$data['shout_id']}");

            $this->bot->delete("uid={$target['uid']}");

            $this->message = "@\"{$user['username']}\"" . $lang->bot_prune_message_user_success . "@\"{$target['username']}\"";

            $this->shout();
        }
    }

}
