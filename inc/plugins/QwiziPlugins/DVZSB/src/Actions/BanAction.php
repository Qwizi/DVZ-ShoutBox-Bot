<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

use Qwizi\DVZSB\Actions\AbstractAction;

class BanAction
{
    public static function ban(int $target) {
        global $mybb, $db;
        $explodeBannedUsers = \explode(",", $mybb->settings['dvz_sb_blocked_users']);

        if (in_array('', $explodeBannedUsers)) {
            $db->update_query('settings', ['value' => $db->escape_string($target)], "name='dvz_sb_blocked_users'");
        } else {
            \array_push($explodeBannedUsers, $target);
            $implodeBannedUsers = \implode(",", $explodeBannedUsers);

            $db->update_query('settings', ['value' => $db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");
        }
        \rebuild_settings();
    }
}
