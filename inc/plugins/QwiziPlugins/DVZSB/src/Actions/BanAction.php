<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

use MyBB;
use DB_Base;
use Qwizi\DVZSB\Actions\ActionInterface;

class BanAction implements ActionInterface
{
    private $mybb;

    private $db;

    public function __construct(MyBB $mybb, DB_Base $db)
    {
        $this->mybb = $mybb;
        $this->db = $db;
    }

    public function execute($target, $additonal = null)
    {
        $explodeBannedUsers = explode(",", $this->mybb->settings['dvz_sb_blocked_users']);

        if (in_array('', $explodeBannedUsers)) {
            $this->db->update_query('settings', ['value' => $this->db->escape_string((int) $target['uid'])], "name='dvz_sb_blocked_users'");
        } else {
            array_push($explodeBannedUsers, $target['uid']);
            $implodeBannedUsers = implode(",", $explodeBannedUsers);

            $this->db->update_query('settings', ['value' => $this->db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
        }

        rebuild_settings();
    }
}
