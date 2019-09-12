<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\ModRequiredInterface;
use Qwizi\DVZSB\Actions\Log;

class PruneCmd extends AbstractCommandBase implements ModRequiredInterface
{
    public function doAction(array $data): void
    {
        if ($this->isMatched($data)) {
            $this->lang->load('dvz_shoutbox_bot_prune');

            $log = new Log($this->db, $data['tag']);

            if (empty($this->getArgs())) {
                $this->lang->error_empty_argument = $this->lang->sprintf($this->lang->error_empty_argument, $this->getCommandPrefix() . $data['command']);
                $this->setError($this->lang->error_empty_argument);
            }

            if (!empty($this->getArgs())) {

                if ($this->getArgs()[0] == '--all') {
                    $user = get_user((int) $data['uid']);

                    $this->setSendMessage(false);
                    $this->deleteShout();

                    $message_log_prune_all = $this->lang->sprintf($this->lang->message_log_prune_all, $user['username']);

                    $log->add($message_log_prune_all);

                    $this->run_hook('dvz_shoutbox_bot_commands_prune_all_commit');
                } else {
                    $user = get_user((int) $data['uid']);
                    $target = get_user_by_username($this->getArgs()[0], ['fields' => 'uid, username']);

                    if (!$this->isValidUser($user) || !$this->isValidUser($target)) {
                        $this->setError($this->lang->error_empty_user);
                    }

                    if (is_super_admin($target['uid']) && $user['uid'] != $target['uid']) {
                        $this->setError($this->lang->error_super_admin);
                    }

                    if (!$this->getError()) {
                        $this->deleteShout("uid={$target['uid']}");
                        $this->setSendMessage(true);

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
                            'message' => $this->getMessage(),
                        ]);
                        $log->add($message_log);
                    }
                }
            } else {
                $this->setReturnedValue(['error' => $this->getError()]);
            }
            if ($this->getSendMessage()) {
                $this->send()->run_hook('dvz_shoutbox_bot_commands_prune_commit');
            }
        }
    }
}
