<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

use DB_Base;
use Qwizi\DVZSB\Interfaces\ActionInterface;

class LogAction implements ActionInterface
{
    const TABLE_NAME = 'dvz_shoutbox_bot_commands_logs';

    private $db;

    private $commandTag;

    public function __construct(DB_Base $db, string $commandTag)
    {
        $this->db = $db;
        $this->commandTag = $commandTag;
    }

    public function execute($target, array $additional)
    {
        $this->db->insert_query(self::TABLE_NAME, [
            'ctag' => $this->db->escape_string($this->commandTag),
            'message' => $this->db->escape_string($target),
            'date' => time()
        ]);
    }
}