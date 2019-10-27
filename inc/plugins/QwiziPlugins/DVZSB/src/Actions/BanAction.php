<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

use Qwizi\DVZSB\Actions\AbstractAction;

class BanAction extends AbstractAction
{
    public function execute($target, $additonal = null)
    {
        $explodeBannedUsers = \explode(",", $this->get('mybb')->settings['dvz_sb_blocked_users']);

        if (in_array('', $explodeBannedUsers)) {
            $this->get('db')->update_query('settings', ['value' => $this->get('db')->escape_string((int) $target['uid'])], "name='dvz_sb_blocked_users'");
        } else {
            \array_push($explodeBannedUsers, $target['uid']);
            $implodeBannedUsers = \implode(",", $explodeBannedUsers);

            $this->get('db')->update_query('settings', ['value' => $this->get('db')->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
        }

        \rebuild_settings();
    }
}
