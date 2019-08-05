<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Exceptions\ApplicationException;
use Qwizi\DVZSB\Exceptions\UserNotFoundException;
use Qwizi\DVZSB\Exceptions\CannotActionMyselfException;

class BanCmd extends Base implements CommandInterface
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
        if (!$this->accessMod()) {
            return;
        }

        if (preg_match($this->pattern($data['command']), $data['text'], $matches)) {
            $this->lang->load('dvz_shoutbox_bot');

            try {

                if (empty($matches[2])) {
                    throw new ApplicationException("Uzyj ".$this->getCommandPrefix().$data['command']." <nazwa_uzytkownika>");
                }

                $target = $this->getUserIdFromUsername($matches[2]);
                $user = $this->getUserNameFromId((int) $data['uid']);
                $explodeBannedUsers = explode(",", $this->mybb->settings['dvz_sb_blocked_users']);

                if (empty($target)) {
                    throw new UserNotFoundException($this->lang->bot_ban_error_empty_user);
                }

                if (empty($user)) {
                    throw new UserNotFoundException($this->lang->bot_ban_error_empty_user);
                }

                if ($target['uid'] == $this->mybb->user['uid']) {
                    throw new CannotActionMyselfException($this->lang->bot_ban_error_ban_myself);
                }

                if (in_array($target['uid'], $explodeBannedUsers)) {
                    throw new ApplicationException($this->lang->bot_ban_error_multiban_user);
                }

                if (in_array('', $explodeBannedUsers)) {
                    $this->db->update_query('settings', ['value' => $this->db->escape_string((int) $target['uid'])], "name='dvz_sb_blocked_users'");
                } else {
                    array_push($explodeBannedUsers, $target['uid']);
                    $implodeBannedUsers = implode(",", $explodeBannedUsers);

                    $this->db->update_query('settings', ['value' => $this->db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
                }

                $this->rebuildSettings();
                
            } catch (ApplicationException $e) {
                $this->setError($e->getMessage());
            }

            $this->lang->bot_ban_message_success = $this->lang->sprintf($this->lang->bot_ban_message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

            $this->setMessage($this->lang->bot_ban_message_success);

            $this->send();

            $this->returned_value = [
                'uid' => $user['uid'],
                'tuid' => $target['uid'],
                'message' => $this->getMessage(),
                'error' => $this->getError()
            ];

            $this->plugins->run_hooks("dvz_shoutbox_bot_commands_ban_commit", $this->returned_value);
        }
    }
}
