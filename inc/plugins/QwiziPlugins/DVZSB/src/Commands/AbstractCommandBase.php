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
    protected $PL;

    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
        $this->mybb = $this->bot->getMybb();
        $this->db = $this->bot->getDb();
        $this->lang = $this->bot->getLang();
        $this->plugins = $this->bot->getPlugins();
        $this->PL = $this->bot->getPL();
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

    public function run_hook($name)
    {
        $this->plugins->run_hooks($name, $this->getReturnedValue());
    }

    public function getUserInfoFromUsername($username)
    {
        return $this->db->fetch_array($this->db->simple_select('users', '*', 'username="' . $username . '"'));
    }

    public function getUserInfoFromId($uid)
    {
        return $this->db->fetch_array($this->db->simple_select('users', '*', 'uid="' . $uid . '"'));
    }

    public function mentionUsername($username)
    {
        return "@\"" . $username . "\"";
    }

    public function createPattern(string $command, string $pattern): string
    {
        $command = $this->baseCommandPattern($command);
        return $pattern = str_replace('{command}', $command, $pattern);
    }

    abstract protected function doAction(array $data): void;
}
