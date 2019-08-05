<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Exceptions\ApplicationException;
use Qwizi\DVZSB\Exceptions\UserNotFoundException;

class PruneCmd extends Base implements CommandInterface
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
                $target = $this->getUserInfoFromUsername($matches[2]);
                $user = $this->getUserInfoFromId((int) $data['uid']);

                if (empty($matches[2])) {
                    $this->deleteShout();
                    $this->plugins->run_hooks("dvz_shoutbox_bot_commands_prune_all_commit", $this->returned_value);
                    $this->setSendMessage(false);
                } else {
                    if (empty($user)) {
                        throw new UserNotFoundException($this->lang->bot_ban_error_empty_user);
                    }

                    if (empty($target)) {
                        throw new UserNotFoundException($this->lang->bot_ban_error_empty_user);
                    }

                    $this->deleteShout("uid={$target['uid']}");
                    $this->setSendMessage(true);
                    $this->lang->bot_prune_message_user_success = $this->lang->sprintf($this->lang->bot_prune_message_user_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

                    $this->setMessage($this->lang->bot_prune_message_user_success);
                }
            } catch (ApplicationException $e) {
                $this->setError($e->getMessage());
            }

            $this->send();

            $this->returned_value = [
                'uid' => $user['uid'],
                'tuid' => $target['uid'],
                'message' => $this->getMessage(),
                'error' => $this->getError(),
            ];

            $this->plugins->run_hooks("dvz_shoutbox_bot_commands_prune_commit", $this->returned_value);
        }
    }
}
