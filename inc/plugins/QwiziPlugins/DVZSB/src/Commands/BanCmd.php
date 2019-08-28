<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\ModRequiredInterface;

class BanCmd extends AbstractCommandBase implements ModRequiredInterface
{
    public function doAction(array $data): void
    {
        if ($this->isMatched($data)) {

            $this->lang->load('dvz_shoutbox_bot_ban');

            if (empty($this->getArgs())) {
                $this->lang->error_empty_argument = $this->lang->sprintf($this->lang->error_empty_argument, $this->getCommandPrefix() . $data['command']);
                $this->setError($this->lang->error_empty_argument);
            } else {
                $user = get_user((int) $data['uid']);
                $targetFromArg = $this->getArgs()[0];
                $target = get_user_by_username($targetFromArg, ['fields' => 'uid, username']);
                $explodeBannedUsers = explode(",", $this->mybb->settings['dvz_sb_blocked_users']);

                if (!$this->isValidUser($user) || !$this->isValidUser($target)) {
                    $this->setError($this->lang->error_empty_user);
                }

                if ($target['uid'] == $this->mybb->user['uid']) {
                    $this->setError($this->lang->error_ban_myself);
                }

                if (in_array($target['uid'], $explodeBannedUsers)) {
                    $this->setError($this->lang->error_multiban_user);
                }

                if (!$this->getError()) {
                    if (in_array('', $explodeBannedUsers)) {
                        $this->db->update_query('settings', ['value' => $this->db->escape_string((int) $target['uid'])], "name='dvz_sb_blocked_users'");
                    } else {
                        array_push($explodeBannedUsers, $target['uid']);
                        $implodeBannedUsers = implode(",", $explodeBannedUsers);

                        $this->db->update_query('settings', ['value' => $this->db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
                    }

                    $this->lang->message_success = $this->lang->sprintf($this->lang->message_success, $this->mentionUsername($user['username']), "@\"{$target['username']}\"");

                    $this->setMessage($this->lang->message_success);

                    rebuild_settings();
                }
            }
            $this->send()->setReturnedValue([
                'uid' => $user['uid'],
                'tuid' => $target['uid'],
                'message' => $this->getMessage(),
                'error' => $this->getError()
            ])->run_hook('dvz_shoutbox_bot_commands_ban_commit');
        }
    }
}