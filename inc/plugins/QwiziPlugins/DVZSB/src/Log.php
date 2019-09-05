<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use DB_Base;

class Log
{
    private $tableName = "dvz_shoutbox_bot_commands_logs";
    private $db;
    private $commandTag;

    public function __construct(DB_Base $db, string $commandTag)
    {
        $this->db = $db;
        $this->commandTag = $commandTag;
    }

    public function getCommandTag()
    {
        return $this->commandTag;
    }

    public function add(string $message)
    {
        $this->db->insert_query($this->tableName, [
            'ctag' => $this->db->escape_string($this->commandTag),
            'message' => $this->db->escape_string($message),
            'date' => time()
        ]);
    }
}