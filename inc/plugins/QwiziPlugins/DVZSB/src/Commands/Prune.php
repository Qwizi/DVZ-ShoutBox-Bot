<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Exceptions\ApplicationException;
use Qwizi\DVZSB\Exceptions\UserNotFoundException;

class Prune extends Base
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
        if (!$this->bot->accessMod()) {
            return;
        }

        if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
            $plugins = $this->bot->getPlugins();

            $this->bot->delete();

            $plugins->run_hooks("dvz_shoutbox_bot_commands_prune_all_commit");
        }

        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $plugins = $this->bot->getPlugins();
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            try {
                $target = $this->bot->getUserInfoFromUsername($matches[1]);
                $user = $this->bot->getUserInfoFromUid((int) $data['uid']);

                if (empty($target)) {
                    throw new UserNotFoundException($lang->bot_ban_error_empty_user);
                }

                if (empty($user)) {
                    throw new UserNotFoundException($lang->bot_ban_error_empty_user);
                }

                $this->bot->delete("uid={$target['uid']}");

            } catch (ApplicationException $e) {
                $this->error = $e->getMessage();
            }

            $lang->bot_prune_message_user_success = $lang->sprintf($lang->bot_prune_message_user_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");

            $this->message = $lang->bot_prune_message_user_success;

            $this->shout();

            $this->returned_value = [
                'uid' => $user['uid'],
                'tuid' => $target['uid'],
                'message' => $this->message,
                'error' => $this->error,
            ];

            $plugins->run_hooks("dvz_shoutbox_bot_commands_prune_commit", $this->returned_value);
        }
    }

}
