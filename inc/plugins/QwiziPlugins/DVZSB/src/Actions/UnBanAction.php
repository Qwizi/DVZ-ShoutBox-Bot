<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

class UnBanAction
{
    public static function unban(int $target) {
        global $mybb, $db;

        $explodeBannedUsers = explode(",", $mybb->settings['dvz_sb_blocked_users']);
        if (($key = array_search($target, $explodeBannedUsers)) !== false) {
            unset($explodeBannedUsers[$key]);
        }

        $implodeBannedUsers = implode(",", $explodeBannedUsers);
        $db->update_query('settings', ['value' => $db->escape_string($implodeBannedUsers)], "name='dvz_sb_blocked_users'");

        \rebuild_settings();
    }
}