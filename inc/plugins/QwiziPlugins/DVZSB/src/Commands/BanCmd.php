<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\AbstractCommand;
use Qwizi\DVZSB\Interfaces\CommandInterface;
use Qwizi\DVZSB\Interfaces\ModRequiredInterface;

class BanCmd extends AbstractCommand implements CommandInterface, ModRequiredInterface
{
    public function handle()
    {
        $argumentValidation = $this->validator->get('not_empty_argument');

        $additional = [
            'prefix' => $this->commandData['prefix'],
            'command' => $this->commandData['command'],
            'arguments' => "<username>"
        ];

        if ($argumentValidation->validate($args[1], $additional)) {
            $userValidation = $this->validator->get('user');

            $user = get_user($this->shoutData['uid']);
            $target = get_user_by_username(trim($args[1]));

            $userValidation->validate($user['uid']);
            $userValidation->validate($target['uid']);
        }

        var_dump($this->validator->getErrors());

        if ($this->validator->isValidated()) {
            $this->action->get('ban')->execute($target);
        }

        /* if ($this->isMatched()) {
            $this->bot->shout('yes');
            $args = $this->getArgs();
            $argumentValidation = $this->validator->getValidation('not_empty_argument');
            if ($argumentValidation->validate($args)) {
                $user = get_user($this->shoutData['uid']);
                $target = get_user_by_username(trim($args[0]), ['fields' => 'uid, username']);

                $this->validator->getValidation('user')->validate($user);
                $this->validator->getValidation('user')->validate($target);
                $this->validator->getValidation('super_admin')->validate($target);
            }

            if ($this->validator->isValidated()) {
                $this->action->getAction('ban')->execute($target);
                $this->action->getAction('log')->execute('log message');
                $message = 'success';
            } else {
                $message = $this->validator->getErrors();
            }

            $this->bot->shout($message);
        }
    }
    public function doAction(array $data): void
    {
        if ($this->isMatched($data)) {
            $this->lang->load('dvz_shoutbox_bot_ban');

            $log = new Log($this->db, $data['tag']);

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

                if (is_super_admin($target['uid']) && $user['uid'] != $target['uid']) {
                    $this->setError($this->lang->error_super_admin);
                }

                if (!$this->getError()) {
                    if (in_array('', $explodeBannedUsers)) {
                        $this->db->update_query('settings', ['value' => $this->db->escape_string((int) $target['uid'])], "name='dvz_sb_blocked_users'");
                    } else {
                        array_push($explodeBannedUsers, $target['uid']);
                        $implodeBannedUsers = implode(",", $explodeBannedUsers);

                        $this->db->update_query('settings', ['value' => $this->db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
                    }

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

                    rebuild_settings();
                } else {
                    $this->setReturnedValue([
                        'error' => $this->getError()
                    ]);
                }
            }
            $this->send()->run_hook('dvz_shoutbox_bot_commands_ban_commit');
        } */
    }
}
