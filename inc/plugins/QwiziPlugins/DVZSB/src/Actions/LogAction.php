<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

use Qwizi\DVZSB\Actions\AbstractAction;

class LogAction extends AbstractAction
{
    const TABLE_NAME = 'dvz_shoutbox_bot_commands_logs';

    public function execute($target, $additional = null)
    {
        $this->get('db')->insert_query(self::TABLE_NAME, [
            'ctag' => $this->get('db')->escape_string($this->get('tag')),
            'message' => $this->get('db')->escape_string($target),
            'date' => time()
        ]);
    }
}