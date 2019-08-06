<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Interfaces\ModRequiredInterface;

class SetBotCmd extends Base implements CommandInterface, ModRequiredInterface
{
    public function pattern(string $commandData): string
    {
        $command = $this->baseCommandPattern($commandData);

        $pattern = '(' . $command . '|' . $command . '[\s](.*))';

        $ReturnedPattern = '/^' . $pattern . '$/';

        return $ReturnedPattern;
    }

    public function doAction(array $data): void
    {
        if (preg_match($this->pattern($data['command']), $data['text'], $matches)) {
            $this->lang->load('dvz_shoutbox_bot');

            $user = $this->getUserInfoFromId($data['uid']);
            $target = $this->getUserInfoFromUsername($matches[2]);

            if (empty($target)) {
                $this->setError($this->lang->bot_ban_error_empty_user);
            }

            if (empty($user)) {
                $this->setError($this->lang->bot_ban_error_empty_user);
            }

            if (!$this->getError()) {
                $this->db->update_query('settings', ['value' => $this->db->escape_string((int) $target['uid'])], "name='dvz_sb_bot_id'");

                $this->lang->bot_setbot_message_success = $this->lang->sprintf($this->lang->bot_setbot_message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");
    
                $this->setMessage($this->lang->bot_setbot_message_success);

                rebuild_settings();
            }

            $this->send()->setReturnedValue([
                'uid' => $user['uid'],
                'tuid' => $target['uid'],
                'message' => $this->getMessage(),
                'error' => $this->getError()
            ])->run_hook('dvz_shoutbox_bot_commands_setbot_commit');
        }
    }
}
