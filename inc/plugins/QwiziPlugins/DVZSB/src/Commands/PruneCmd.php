<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\ModRequiredInterface;

class PruneCmd extends AbstractCommandBase implements ModRequiredInterface
{
    private $pattern = "/^({command}|{command}[\s](.*))$/";

    public function doAction(array $data): void
    {
        if (preg_match($this->createPattern($data['command'], $this->pattern), $data['text'], $matches)) {

            $this->lang->load('dvz_shoutbox_bot');

            if (empty($matches[2])) {
                $this->setSendMessage(false);
                $this->deleteShout();
                $this->run_hook('dvz_shoutbox_bot_commands_prune_all_commit');
            } else {
                $target = $this->getUserInfoFromUsername($matches[2]);
                $user = $this->getUserInfoFromId((int) $data['uid']);

                if (empty($user)) {
                    $this->setError($this->lang->bot_ban_error_empty_user);
                }

                if (empty($target)) {
                    $this->setError($this->lang->bot_ban_error_empty_user);
                }

                if (!$this->getError()) {
                    $this->deleteShout("uid={$target['uid']}");
                    $this->setSendMessage(true);
                    $this->lang->bot_prune_message_user_success = $this->lang->sprintf($this->lang->bot_prune_message_user_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

                    $this->setMessage($this->lang->bot_prune_message_user_success);
                }


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
