<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

class Command
{
    private static $instance = null;
    private $cache;
    private $db;
    private $cacheTableName = 'dvz_shoutbox_bot';
    private $tableName = 'dvz_shoutbox_bot_commands';
    private $commands = [];

    public function __construct($cache, $db)
    {
        $this->cache = $cache;
        $this->db = $db;
        $this->commands = $this->getCommandsFromCache();
    }
    public static function createInstance($cache, $db)
    {
        if (static::$instance === null) {
            static::$instance = new self($cache, $db);
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
        $pluginCache = $this->cache->read($this->cacheTableName);
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

    public function updateCache()
    {
        /* $commandsArray = [];
        $query = $this->db->simple_select($this->tableName, "*");
        while($row = $this->db->fetch_array($query)) {
            $commandsArray['commands'] = $row;
        }
        $this->cache->update($this->cacheTableName, $commandsArray); */

		$query = $this->db->simple_select($this->tableName);
		$cmds = [];
		while($c = $this->db->fetch_array($query))
		{
			$cmds[$c['tag']] = $c;
		}
		$this->cache->update($this->cacheTableName, ['commands' => $cmds]);
    }
}