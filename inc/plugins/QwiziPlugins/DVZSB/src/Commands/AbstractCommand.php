<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use MyLanguage;
use Qwizi\DVZSB\Bot;
use Qwizi\DVZSB\CommandAction;
use Qwizi\DVZSB\CommandValidator;

class AbstractCommand
{
    /** @var MyLanguage */
    protected $lang;

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
        $this->args = $this->createArgs();
    }

    /**
     * Set Mybb Lang instance
     *
     * @param MyLanguage $lang Lang Instance
     *
     * @return void
     */
    public function setLang(MyLanguage $lang)
    {
        $this->lang = $lang;
        
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
     * Create command pattern
     *
     * @param string $pattern Pattern
     *
     * @return string
     */
    private function createPattern(string $pattern): string
    {
        $prefix = $this->commandData['prefix'];
        $command = "\\".$prefix. preg_quote($this->commandData['command']);
        $replacedPattern = str_replace('{command}', $command, $pattern);
        return $replacedPattern;
    }

    /**
     * Create args
     * 
     * @return array
     */
    public function createArgs()
    {
        preg_match($this->pattern, $this->shoutData['text'], $matches);
        $args = [];
        if (!empty($matches[2])) {
            if (preg_match_all('/"([^"]+)"/', $matches[2], $m)) {
                $args = $m[1];
            } else {
                $args = explode(" ", $matches[2]);
            }
        }
        return $args;
    }
}
