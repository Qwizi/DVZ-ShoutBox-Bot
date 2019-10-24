<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use DB_Base;
use datacache;

class CommandManager
{
    const TABLE_NAME = 'dvz_shoutbox_bot_commands';
    const CACHE_NAME = 'dvz_shoutbox_bot';

    /**
     * @var CommandManager
     */
    private static $instance = null;

    /**
     * @var DB_Base
     */
    private $db;

    /**
     * @var datacache
     */
    private $cache;

    /**
     * @var $commands
     */
    private $commands = [];

    public function __construct(DB_Base $db, datacache $cache)
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->commands = $this->getCommandsFromCache();
    }

    /**
     * Create instance of the command manager
     *
     * @param DB_Base $db MyBB database object
     * @param datacache $cache MyBB cache object
     *
     * @return CommandManager The created instance
     */
    public static function createInstance(DB_Base $db, datacache $cache)
    {
        if (static::$instance === null) {
            static::$instance = new self($db, $cache);
        }
        return static::$instance;
    }

    /**
     * Get a prior created command manager instance
     *
     * @return bool|CommandManager The prior created
     *                              instance, or false if
     *                              not created
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            return false;
        }
        return static::$instance;
    }

    /**
     * Short method getInstance
     *
     * @return bool|CommandManager The prior created
     *                              instance, or false if
     *                              not created
     */
    public static function i()
    {
        return static::getInstance();
    }
    
    /**
     * Set commands
     *
     * @param array $commands
     */
    private function setCommands(array $commands): void
    {
        $this->commands[] = $commands;
    }

    /**
     * Get commands
     *
     * @return array Get commands
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Get commands from the cache
     *
     * @return array Get commands from the cache
     */
    private function getCommandsFromCache(): ?array
    {
        $pluginCache = $this->cache->read(self::CACHE_NAME);
        return $pluginCache['commands'];
    }

    /**
     * Update commands cache
     *
     * @return array
     */
    public function updateCache(): array
    {
        $query = $this->db->simple_select(self::TABLE_NAME);
        while ($c = $this->db->fetch_array($query)) {
            $cmds[$c['tag']] = $c;
        }
        $this->cache->update(self::CACHE_NAME, ['commands' => $cmds]);
        return $cmds;
    }

    /**
     * Create commands
     *
     * @param array $commandData
     */
    public function createCommand(array $commandData): void
    {
        if (empty($commandData)) {
            return;
        }

        if (count($commandData) > 1) {
            foreach ($commandData as $key => $value) {
                foreach ($value as $v) {
                    $this->db->escape_string($v);
                }
            }
            $this->db->insert_query_multiple(self::TABLE_NAME, $commandData);
        } else {
            foreach ($commandData as $key => $value) {
                $this->db->escape_string($value);
            }

            $this->db->insert_query(self::TABLE_NAME, $commandData);
        }
        
        $commands = $this->updateCache();
        $this->setCommands($commands);
    }

    public function getCommandByTag(string $tag): array
    {
        try {
            $query = $this->db->simple_select(self::TABLE_NAME, '*', "tag={$tag}");

            if ($this->db->num_rows($query) <= 0) {
                throw new CommandNotFoundException('Command with tag {$tag} not found');
            }

            $command = $this->db->fetch_array($query);
        } catch (CommandNotFoundException $e) {
            echo 'Error message: ' . $e->getMessage();
        }
        return $command;
    }
}
