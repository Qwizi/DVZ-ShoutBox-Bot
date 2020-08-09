<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use DB_Base;
use datacache;
use Qwizi\DVZSB\Exceptions\CommandNotFoundException;
use Exception;

class CommandManager
{
    const TABLE_NAME = 'dvz_shoutbox_bot_commands';

    /**
     * Get commands from the cache
     *
     * @return array Get commands from the cache
     */
    private static function getCommandsFromCache(): array
    {
        global $cache;
        $commandsCache = $cache->read(self::TABLE_NAME);
        foreach ($commandsCache as &$command) {
            $command['namespace'] = str_replace('//', '\\', $command['namespace']);
        }
        return $commands;
    }

    public static function getCommands()
    {
        global $db;
        $query = $db->simple_select(self::TABLE_NAME, '*');
        $commands = [];
        while($row = $db->fetch_array($query)) $commands[] = $row;
        return $commands;
    }

    public static function getCommand(string $tag) {
        global $db;
        $tag = $db->escape_string($tag);
        $query = $db->simple_select(self::TABLE_NAME, '*', 'tag="'.$tag.'"', ['limit' => 1]);
        $command = [];
        $command = $db->fetch_array($query);

        if (!empty($command)) {
            $comamnd['tag'] = \htmlspecialchars_uni($command['tag']);
            $comamnd['name'] = \htmlspecialchars_uni($comamnd['name']);
            $command['description'] = \htmlspecialchars_uni($command['description']);
            $command['namespace'] = \htmlspecialchars_uni($command['namespace']);
            $command['namespace'] = str_replace('//', '\\', $command['namespace']);
            $command['activated'] = boolval($command['activated']);
        }
        return $command;
    }

    /**
     * Update commands cache
     *
     * @return array
     */
    public static function updateCache(): array
    {
        global $db, $cache;
        $query = $db->simple_select(self::TABLE_NAME, '*');
        $commands = [];
        while ($row =$db->fetch_array($query)) {
            $commands[$row['tag']] = $row;
        }
        $cache->update(self::TABLE_NAME, ['commands' => $commands]);
        return $commands;
    }

    public static function addCommands(string $nameSpace, array $commandsData) {
        global $db;
        foreach ($commandsData as &$command) {
            if (!key_exists('namespace', $command)) {
                $command['namespace'] = $nameSpace.ucfirst($command['tag']). 'Cmd';
            }
            foreach ($command as $key => $value) {
                if ($key !== 'namespace') {
                    $db->escape_string($value);
                }
            }
        }

        $db->insert_query_multiple(self::TABLE_NAME, $commandsData);

        static::updateCache();
    }

    /**
     * Create commands
     *
     * @param array $commandData
     */
    // CommandManager::i()->createCommand(string)
    public function createCommand(string $nameSpace, array $commandData): void
    {
        if (empty($commandData)) {
            return;
        }

        foreach ($commandData as &$command) {
            if (!key_exists('namespace', $command)) {
                $command['namespace'] = $nameSpace.ucfirst($command['tag']). 'Cmd';
                $command['namespace'] = \str_replace('//', '\\', $command['namespace']);
            }
            foreach ($command as $key => $value) {
                if ($key !== 'namespace') {
                    $this->db->escape_string($value);
                }
            }
        }

        $this->db->insert_query_multiple(self::TABLE_NAME, $commandData);

        $this->updateCache();
    }

    public function getCommandByTag(string $tag): array
    {
        try {
            $query = $this->db->simple_select(self::TABLE_NAME, '*', "tag='".$tag."'");

            if ($this->db->num_rows($query) <= 0) {
                throw new CommandNotFoundException('Command with tag {$tag} not found');
            }

            $command = $this->db->fetch_array($query);
        } catch (CommandNotFoundException $e) {
            echo 'Error message: ' . $e->getMessage();
        }
        return $command;
    }

    public function getCommandByCommand(string $commandName): array
    {
        $query = $this->db->simple_select(self::TABLE_NAME, '*', "command='".$commandName."'", ['limit' => 1]);

        if ($this->db->num_rows($query) <= 0) {
            return [];
        }

        $command = $this->db->fetch_array($query);
        $command['namespace'] = \str_replace('//', '\\', $command['namespace']);
        return $command;
    }
}
