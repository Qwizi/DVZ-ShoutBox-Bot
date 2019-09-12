<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\ModRequiredInterface;
use Qwizi\DVZSB\Log;

class SetBotCmd extends AbstractCommandBase implements ModRequiredInterface
{
    public function doAction(array $data): void
    {
        if ($this->isMatched($data)) {
            $this->lang->load('dvz_shoutbox_bot_setbot');

            $log = new Log($this->db, $data['tag']);

            if (empty($this->getArgs())) {
                $this->lang->error_empty_argument = $this->lang->sprintf($this->lang->error_empty_argument, $this->getCommandPrefix() . $data['command']);
                $this->setError($this->lang->error_empty_argument);
            } else {
                $user = get_user((int) $data['uid']);
                $target = get_user_by_username($this->getArgs()[0], ['fields' => 'uid, username']);

                if (!$this->isValidUser($user) || !$this->isValidUser($target)) {
                    $this->setError($this->lang->error_empty_user);
                }

                if (!$this->getError()) {
                    $this->db->update_query('settings', ['value' => $this->db->escape_string((int) $target['uid'])], "name='dvz_sb_bot_id'");

                    $message_success = $this->lang->sprintf(
                        $this->lang->message_success,
                        $this->mentionUsername($user['username']),
                        $this->mentionUsername($target['username'])
                    );

                    $message_log = $this->lang->sprintf(
                        $this->lang->message_success,
                        $user['username'],
                        $target['username']
                    );

                    $this->setMessage($message_success);
                    $this->setReturnedValue([
                        'uid' => $user['uid'],
                        'tuid' => $target['uid'],
                        'message' => $this->getMessage()
                        ]);
                    
                    $log->add($message_log);
                    
                    rebuild_settings();
                } else {
                    $this->setReturnedValue(['error' => $this->getError()]);
                }
            }
            $this->send()->run_hook('dvz_shoutbox_bot_commands_setbot_commit');
        }
    }
}
