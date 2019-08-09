<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Bot;

abstract class AbstractCommandBase
{
    public $returned_value = [];

    private $bot;
    private $commandPrefix;
    private $error;
    private $message;
    private $sendMessage;

    protected $mybb;
    protected $db;
    protected $lang;
    protected $plugins;

    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
        $this->mybb = $this->bot->getMybb();
        $this->db = $this->bot->getDb();
        $this->lang = $this->bot->getLang();
        $this->plugins = $this->bot->getPlugins();
        $this->commandPrefix = $this->bot->settings('commands_prefix');
        $this->sendMessage = true;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getCommandPrefix()
    {
        return $this->commandPrefix;
    }

    public function getSendMessage()
    {
        return $this->sendMessage;
    }

    public function getReturnedValue()
    {
        return $this->returned_value;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setError(string $error): void
    {
        $this->error = $error;
    }

    public function setSendMessage(bool $value)
    {
        $this->sendMessage = $value;
    }

    public function setReturnedValue(array $value)
    {
        $this->returned_value = $value;
        return $this;
    }

    public function send()
    {
        if ((bool) $this->getSendMessage()) {
            if (isset($this->error)) {
                $this->bot->shout($this->error);
                return $this;
            } else {
                $this->bot->shout($this->message);
                return $this;
            }
        }
    }

    public function deleteShout($where = "")
    {
        $this->bot->delete($where);
    }

    public function baseCommandPattern(string $command): string
    {
        return "\\" . $this->getCommandPrefix() . preg_quote($command);
    }

    public function createPattern(string $command, string $pattern): string
    {
        $command = $this->baseCommandPattern($command);
        return str_replace('{command}', $command, $pattern);
    }

    public function isValidUser($user)
    {
        if (is_array($user) && !empty($user)) {
            return true;
        }
        return false;
    }

    public function mentionUsername($username)
    {
        return "@\"" . $username . "\"";
    }

    public function run_hook($name)
    {
        $this->plugins->run_hooks($name, $this->getReturnedValue());
    }

    public function user_last_shout_time($uid, $matches)
    {
        global $db;
        return $this->db->fetch_field(
            $this->db->simple_select('dvz_shoutbox s', 'date', 'uid=' . (int)$uid . ' AND s.text LIKE "'.$matches.'%"', [
                'order_by'  => 'date',
                'order_dir' => 'desc',
                'limit'     => 1,
            ]),
            'date'
        );
    }

    public function antiflood_pass($matches)
    {
        return (( TIME_NOW - $this->user_last_shout_time($this->mybb->user['uid'], $matches) ) > $this->settings['dvz_sb_antiflood']
        );
    }

    abstract protected function doAction(array $data): void;
}
