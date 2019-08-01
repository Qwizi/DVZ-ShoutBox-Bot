<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

class Prune extends Base
{
    public function doAction(array $data): void
    {
        if (!$this->bot->accessMod()) {
            return;
        }

        if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
            $this->bot->delete();
        }

        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $user = $this->bot->getUserInfoFromUid($data['uid']);
            $target = $this->bot->getUserInfoFromUsername($matches[1]);
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            $this->bot->delete("id={$data['shout_id']}");

            $this->bot->delete("uid={$target['uid']}");

            $lang->bot_prune_message_user_success = $lang->sprintf($lang->bot_prune_message_user_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

            $this->message = $lang->bot_prune_message_user_success;

            $this->shout();
        }
    }

}
