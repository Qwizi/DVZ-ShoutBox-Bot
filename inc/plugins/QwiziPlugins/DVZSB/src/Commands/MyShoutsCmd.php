<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

class MyShoutsCmd extends AbstractCommandBase
{
    private $pattern = "/^({command})$/";

    public function doAction(array $data): void
    {
        if (preg_match($this->createPattern($data['command'], $this->pattern), $data['text'], $matches)) { 

            $this->lang->load('dvz_shoutbox_bot_myshouts');
            
            $user = get_user((int)$data['uid']);

            $our_shouts_query = $this->db->query("SELECT count(id) as id, uid FROM ".TABLE_PREFIX."dvz_shoutbox s WHERE s.uid='".$data['uid']."' AND s.text IS NOT NULL");
            $our_shouts = $this->db->fetch_field($our_shouts_query, "id");

            $this->lang->success_message = $this->lang->sprintf($this->lang->success_message, $this->mentionUsername($user['username']), $our_shouts);

            $this->setMessage($this->lang->success_message);

            $this->send();

        }
    }
}
