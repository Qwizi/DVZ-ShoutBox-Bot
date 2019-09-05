<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\ModRequiredInterface;
use Qwizi\DVZSB\Log;

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
                    $this->setSendMessage(false);
                    $this->deleteShout();
                    $this->run_hook('dvz_shoutbox_bot_commands_prune_all_commit');
                } else {
                    $user = get_user((int) $data['uid']);
                    $target = get_user_by_username($this->getArgs()[0], ['fields' => 'uid, username']);
    
                    if (!$this->isValidUser($user) || !$this->isValidUser($target)) {
                        $this->setError($this->lang->error_empty_user);
                    }
    
                    if (!$this->getError()) {
                        $this->deleteShout("uid={$target['uid']}");
                        $this->setSendMessage(true);
                        $this->lang->message_success = $this->lang->sprintf($this->lang->message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");
    
                        $this->setMessage($this->lang->message_success);

                        $log->add($this->getMessage());
                    }
                }
            }
            if ($this->getSendMessage() == true) {
                $this->send()->setReturnedValue([
                    'uid' => $user['uid'],
                    'tuid' => $target['uid'],
                    'message' => $this->getMessage(),
                    'error' => $this->getError(),
                ])->run_hook('dvz_shoutbox_bot_commands_prune_commit');
            }
        }
    }
}
