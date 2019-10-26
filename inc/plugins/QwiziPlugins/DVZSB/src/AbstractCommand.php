<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use Qwizi\DVZSB\Bot;
use Qwizi\DVZSB\CommandAction;
use Qwizi\DVZSB\CommandValidator;

class AbstractCommand
{
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

    public function createArgs()
    {
        if (preg_match('/^\"(.*)\"$/', $this->shoutData['text'], $m)) {
            $args[] = $m[1];
        } else {
            $args = explode(" ", $this->shoutData['text']);
        }
        return $args;
    }
}
