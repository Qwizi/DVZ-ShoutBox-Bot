<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use Mybb;
use DB_Base;
use Mylanguage;
use PluginSystem;
use Exception;
use Qwizi\DVZSB\Bot;

class AbstractCommand extends Exception
{
    /** @var MyBB */
    protected $mybb;

    /** @var DB_Base */
    protected $db;

    /** @var Mylanguage */
    protected $lang;

    /** @var PluginSystem */
    protected $plugins;

    /** @var Bot */
    protected $bot;

    /** @var CommandValidator */
    protected $validator;

    /** @var CommandAction */
    protected $action;

    /** @var string */
    private $pattern;

    /** @var array */
    private $args = [];

    /** @var array */
    private $values = [];

    /** @var array */
    protected $shoutData = [];

    /** @var array */
    protected $commandData = [];

    public function __construct(array $shoutData, array $commandData)
    {
        $this->shoutData = $shoutData;
        $this->commandData = $commandData;
        $this->pattern = $this->createPattern("/^({command}|{command}[\s]((.*)))$/");
    }

    /**
     * Set Mybb instance
     *
     * @return void
     */
    public function setMybb(MyBB $mybb): self
    {
        $this->mybb = $mybb;

        return $this;
    }

    /**
     * Set MyBB database instance
     *
     * @return void
     */
    public function setDb(DB_Base $db): self
    {
        $this->db = $db;

        return $this;
    }

    /**
     * Set MyBB language instance
     *
     * @return void
     */
    public function setLang(Mylanguage $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Set Mybb plugins system instance
     *
     * @return void
     */
    public function setPlugins(PluginSystem $plugins): self
    {
        $this->plugins = $plugins;

        return $this;
    }

    /**
     * Set bot instance
     *
     * @param Bot $bot Bot Instance
     *
     * @return void
     */
    public function setBot(Bot $bot): self
    {
        $this->bot = $bot;

        return $this;
    }

    /**
     * Set command validator instancce
     *
     * @return void
     */
    public function setValidator(CommandValidator $validator): self
    {
        $this->validator = $validator;

        return $this;
    }

    public function setAction(CommandAction $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get command arguments
     *
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Set command args
     *
     * @param array $args Arguments
     *
     * @return void
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * Get the value of values
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Set the value of values
     *
     * @return  self
     */
    public function setValues($values): self
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Load lang
     *
     * @return void
     */
    public function loadLang(): self
    {
        $this->lang->load("dvz_shoutbox_bot_{$this->commandData['tag']}");

        return $this;
    }

    /**
     * Create command pattern
     *
     * @param string $pattern Pattern
     *
     * @return string
     */
    private function createPattern(string $pattern): string
    {
        $prefix = $this->mybb->settings['dvz_sb_bot_command_prefix'];
        $command = "\\".$prefix. preg_quote($this->commandData['command']);
        return str_replace('{command}', $command, $pattern);
    }

    /**
     * Match command
     *
     * @return bool
     */
    private function matchCommand(): bool
    {
        if (preg_match($this->pattern, $this->shoutData['text'], $matches)) {
            if (!empty($matches[2])) {
                if (preg_match('/^\"(.*)\"$/', $matches[2], $m)) {
                    $args[] = $m[1];
                } else {
                    $args = explode(" ", $matches[2]);
                }
                $this->setArgs($args);
            }
            return true;
        }
        return false;
    }

    /**
     * Check is command matched
     *
     * @return bool
     */
    public function isMatched(): bool
    {
        return $this->matchCommand();
    }

    /**
     * Send message
     *
     * @return void
     */
    public function send()
    {
        $messageType = $this->validator::isValidated() ? $this->validator::getSuccessMessage() : $this->validator::getSuccessMessage();

        $this->bot->shout($messageType);
    }
}
