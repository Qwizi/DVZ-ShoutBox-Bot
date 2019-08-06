<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Interfaces\ModRequiredInterface;

class UnBanCmd extends Base implements CommandInterface, ModRequiredInterface
{
    public function pattern(string $commandData): string
    {
        /* $pattern = '/^\\' . $this->bot->settings('commands_prefix') . preg_quote($command) . '$/'; */

        $command = $this->baseCommandPattern($commandData);

        $pattern = '(' . $command . '|' . $command . '[\s](.*))';

        $ReturnedPattern = '/^' . $pattern . '$/';

        return $ReturnedPattern;
    }

    public function doAction(array $data): void
    {
        if (preg_match($this->pattern($data['command']), $data['text'], $matches)) {
            $this->lang->load('dvz_shoutbox_bot');

            $target = $this->getUserInfoFromUsername($matches[2]);
            $user = $this->getUserInfoFromId((int) $data['uid']);
            $explodeBannedUsers = explode(",", $this->mybb->settings['dvz_sb_blocked_users']);

            if (empty($target)) {
                $this->setError($this->lang->bot_ban_error_empty_user);
            }

            if (empty($user)) {
                $this->setError($this->lang->bot_ban_error_empty_user);
            }

            if ($target['uid'] == $this->mybb->user['uid']) {
                $this->setError($this->lang->bot_ban_error_ban_myself);
            }

            if (!in_array($target['uid'], $explodeBannedUsers)) {
                $this->setError($this->lang->bot_unban_no_ban);
            }

            if (!$this->getError()) {
                if (($key = array_search($target['uid'], $explodeBannedUsers)) !== false) {
                    unset($explodeBannedUsers[$key]);
                }

                $implodeBannedUsers = implode(",", $explodeBannedUsers);
                $this->db->update_query('settings', ['value' => $this->db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");

                $this->lang->bot_unban_message_success = $this->lang->sprintf($this->lang->bot_unban_message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

                $this->setMessage($this->lang->bot_unban_message_success);

                rebuild_settings();
            }

            $this->send()->setReturnedValue([
                'uid' => $user['uid'],
                'tuid' => $target['uid'],
                'message' => $this->getMessage(),
                'error' => $this->getError()
            ])->run_hook('dvz_shoutbox_bot_commands_unban_commit');
        }
    }
}
