<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Interfaces\ModRequiredInterface;

class SetBotCmd extends AbstractCommandBase implements ModRequiredInterface
{
    private $pattern = "/^({command}|{command}[\s](.*))$/";

    public function doAction(array $data): void
    {
        if (preg_match($this->createPattern($data['command'], $this->pattern), $data['text'], $matches)) {
            $this->lang->load('dvz_shoutbox_bot_setbot');

            if (empty($matches[2])) {
                $this->lang->error_empty_argument = $this->lang->sprintf($this->lang->error_empty_argument, $this->getCommandPrefix() . $data['command']);
                $this->setError($this->lang->error_empty_argument);
            } else {

                $user = get_user((int) $data['uid']);
                $target = get_user_by_username($matches[2], ['fields' => 'uid, username']);

                if (!$this->isValidUser($user) || !$this->isValidUser($target)) {
                    $this->setError($this->lang->error_empty_user);
                }

                if (!$this->getError()) {
                    $this->db->update_query('settings', ['value' => $this->db->escape_string((int) $target['uid'])], "name='dvz_sb_bot_id'");

                    $this->lang->message_success = $this->lang->sprintf($this->lang->message_success, "@\"{$user['username']}\"", "@\"{$target['username']}\"");
        
                    $this->setMessage($this->lang->message_success);

                    rebuild_settings();
                }
            }

            $this->send()->setReturnedValue([
                'uid' => $user['uid'],
                'tuid' => $target['uid'],
                'message' => $this->getMessage(),
                'error' => $this->getError()
            ])->run_hook('dvz_shoutbox_bot_commands_setbot_commit');
        }
    }
}
