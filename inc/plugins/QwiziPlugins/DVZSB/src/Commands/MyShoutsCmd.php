<?php
declare (strict_types = 1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Interfaces\CommandInterface;

class MyShoutsCmd extends Base implements CommandInterface
{
    public function pattern(string $commandData): string
    {
        $command = $this->baseCommandPattern($commandData);

        $pattern = '(' . $command . ')';

        $ReturnedPattern = '/^' . $pattern . '$/';

        return $ReturnedPattern;
    }

    public function doAction(array $data): void
    {
        if (preg_match($this->pattern($data['command']), $data['text'], $matches)) {
            $this->lang->load('dvz_shoutbox_bot');

            $user = $this->getUserInfoFromId($data['uid']);

            if (empty($user)) {
                $this->setError($this->lang->bot_ban_error_empty_user);
            }

            if (!$this->getError()) {
                $query = $this->db->query("SELECT count(id) as id FROM ".TABLE_PREFIX."dvz_shoutbox WHERE uid='".$user['uid']."' AND NOT text=NULL");
                $sb_num = $this->db->fetch_field($query, "id");

                $this->setMessage("@\"{$user['username']}\", posiadasz {$sb_num} wpisów na sb!");
            }

            $this->send();
        }
    }
}