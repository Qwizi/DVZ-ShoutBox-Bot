<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

class Command
{
    private static $instance = null;
    private $PL;
    private $table_name = 'dvz_shoutbox_bot';
    private $commands = [];

    public function __construct($PL)
    {
        $this->PL = $PL;
        $this->commands = $this->getCommandsFromCache();
    }
    public static function createInstance($PL)
    {
        if (static::$instance === null) {
            static::$instance = new self($PL);
        }
        return static::$instance;
    }

    public static function i()
    {
        if (static::$instance === null) {
            return false;
        }
        return static::$instance;
    }
    
    private function getCommandsFromCache()
    {
        $pluginCache = $this->PL->cache_read($this->table_name);
        return $pluginCache['commands'];
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public function setCommands($commands)
    {
        $this->commands = $commands;
    }
}