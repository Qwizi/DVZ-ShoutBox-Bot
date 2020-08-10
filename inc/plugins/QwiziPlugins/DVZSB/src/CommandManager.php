<?php

declare (strict_types = 1);

namespace Qwizi\DVZSB;

class CommandManager
{
    const TABLE_NAME = 'dvz_shoutbox_bot_commands';

    public static function getCommands()
    {
        global $db;
        $query = $db->simple_select(self::TABLE_NAME, '*');
        $commands = [];
        while ($row = $db->fetch_array($query)) {
            $row['tag'] = \htmlspecialchars_uni($row['tag']);
            $row['name'] = \htmlspecialchars_uni($row['name']);
            $row['description'] = \htmlspecialchars_uni($row['description']);
            $row['namespace'] = \htmlspecialchars_uni($row['namespace']);
            $row['namespace'] = str_replace('//', '\\', $row['namespace']);
            $row['activated'] = boolval($command['activated']);
            $commands[] = $row;
        };
        return $commands;
    }

    public static function getCommand(string $tag)
    {
        global $db;
        $tag = $db->escape_string($tag);
        $query = $db->simple_select(self::TABLE_NAME, '*', 'tag="' . $tag . '"', ['limit' => 1]);
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

    public static function addCommands(string $nameSpace, array $commandsData)
    {
        global $db;
        foreach ($commandsData as &$command) {
            if (!key_exists('namespace', $command)) {
                $command['namespace'] = $nameSpace . ucfirst($command['tag']) . 'Cmd';
            }
            foreach ($command as $key => $value) {
                if ($key !== 'namespace') {
                    $db->escape_string($value);
                }
            }
        }

        $db->insert_query_multiple(self::TABLE_NAME, $commandsData);
    }
}
