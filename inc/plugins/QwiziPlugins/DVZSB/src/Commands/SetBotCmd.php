<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Exceptions\ApplicationException;
use Qwizi\DVZSB\Exceptions\UserNotFoundException;

class SetBotCmd extends Base implements CommandInterface
{
    public function pattern(string $commandData): string
    {
        /* $pattern = '/^\\' . $this->bot->settings('commands_prefix') . preg_quote($command) . '$/'; */

        $command = $this->baseCommandPattern($commandData);

        $pattern = '(' . $command . '|' . $command . '[\s]([0-9]+))';

        $ReturnedPattern = '/^' . $pattern . '$/';

        return $ReturnedPattern;
    }

    public function doAction(array $data): void
    {
        if (!$this->accessMod()) {
            return;
        }

        if (preg_match($this->pattern($data['command']), $data['text'], $matches)) {
            $this->lang->load('dvz_shoutbox_bot');

            try {
                $user = $this->getUserIdFromUsername($data['uid']);
                $target = $this->getUserNameFromId($matches[2]);

                if (empty($target)) {
                    throw new UserNotFoundException($this->lang->bot_ban_error_empty_user);
                }

                if (empty($user)) {
                    throw new UserNotFoundException($this->lang->bot_ban_error_empty_user);
                }

                $this->db->update_query('settings', ['value' => $this->db->escape_string((int) $target['uid'])], "name='dvz_sb_bot_id'");

                $this->rebuildSettings();

            } catch (ApplicationException $e) {
                $this->setError($e->getMessage());
            }

            $this->lang->bot_setbot_message_success = $this->lang->sprintf($this->lang->bot_setbot_message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

            $this->setMessage($this->lang->bot_setbot_message_success);
            
            $this->send();

            $this->returned_value = [
                'uid' => $user['uid'],
                'tuid' => $target['uid'],
                'message' => $this->getMessage(),
                'error' => $this->getError()
            ];

            $this->plugins->run_hooks("dvz_shoutbox_bot_commands_setbot_commit", $this->returned_value);
        }
    }
}
