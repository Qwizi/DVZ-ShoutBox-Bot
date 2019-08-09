<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

class MyShoutsCmd extends AbstractCommandBase
{
    private $pattern = "/^({command})$/";

    public function doAction(array $data): void
    {
        if (preg_match($this->createPattern($data['command'], $this->pattern), $data['text'], $matches)) { 

            $this->lang->load('dvz_shoutbox_bot');
            
            $user = get_user((int)$data['uid']);

            $our_shouts_query = $this->db->query("SELECT count(id) as id, uid FROM ".TABLE_PREFIX."dvz_shoutbox WHERE uid='".$data['uid']." AND text IS NOT NULL'");
            $our_shouts = $this->db->fetch_field($our_shouts_query, "id");

            $message = sprintf("%s napisaleś na shoutboxie %d wiadomosci!", $this->mentionUsername($user['username']), $our_shouts);

            $this->setMessage($message);

            $this->send();

        }
    }
}
