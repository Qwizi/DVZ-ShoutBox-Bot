<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

class TopShoutersCmd extends AbstractCommandBase
{
    public function doAction(array $data): void
    {
        if ($this->isMatched($data)) {
            $this->lang->load('dvz_shoutbox_bot_topshouters');

            $query = $this->db->query("
                SELECT s.uid, u.username, u.usergroup, u.displaygroup, u.uid, u.avatar, count(*) as totalshouts 
                FROM " . TABLE_PREFIX . "dvz_shoutbox s
                LEFT JOIN " . TABLE_PREFIX . "users u ON (u.uid=s.uid)
                WHERE s.text IS NOT NULL
				GROUP BY s.uid 
				ORDER BY totalshouts 
                DESC LIMIT 10
            ");

            $message = "TOP - 10\n";
            while ($row = $this->db->fetch_array($query)) {
                $message .=  sprintf("%s - %d\n", $this->mentionUsername($row['username']), $row['totalshouts']);
            }

            $this->setMessage($message);
            $this->send();
        }
    }
}
