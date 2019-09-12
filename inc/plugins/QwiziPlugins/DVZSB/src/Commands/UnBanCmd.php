<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\ModRequiredInterface;
use Qwizi\DVZSB\Actions\Log;

class UnBanCmd extends AbstractCommandBase implements ModRequiredInterface
{
    public function doAction(array $data): void
    {
        if ($this->isMatched($data)) {
            $this->lang->load('dvz_shoutbox_bot_unban');

            $log = new Log($this->db, $data['tag']);

            if (empty($this->getArgs())) {
                $this->lang->error_empty_argument = $this->lang->sprintf($this->lang->error_empty_argument, $this->getCommandPrefix() . $data['command']);
                $this->setError($this->lang->error_empty_argument);
            } else {
                $user = get_user((int) $data['uid']);
                $target = get_user_by_username($this->getArgs()[0], ['fields' => 'uid, username']);
                $explodeBannedUsers = explode(",", $this->mybb->settings['dvz_sb_blocked_users']);

                if (!$this->isValidUser($user) || !$this->isValidUser($target)) {
                    $this->setError($this->lang->error_empty_user);
                }

                if ($target['uid'] == $this->mybb->user['uid']) {
                    $this->setError($this->lang->error_unban_myself);
                }

                if (!in_array($target['uid'], $explodeBannedUsers)) {
                    $this->setError($this->lang->no_ban);
                }

                if (!$this->getError()) {
                    if (($key = array_search($target['uid'], $explodeBannedUsers)) !== false) {
                        unset($explodeBannedUsers[$key]);
                    }

                    $implodeBannedUsers = implode(",", $explodeBannedUsers);
                    $this->db->update_query('settings', ['value' => $this->db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");

                    $this->lang->message_success = $this->lang->sprintf($this->lang->message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

                    $this->setMessage($this->lang->message_success);

                    rebuild_settings();

                    $log->add($this->getMessage());
                }
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
