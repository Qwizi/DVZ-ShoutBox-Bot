<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;


class Bot
{
    /*
    Delete shout
    */
    public static function deleteShout($id)
    {
        global $mybb, $db;

        if ($mybb->settings['dvz_sb_sync']) {
            return $db->update_query('dvz_shoutbox', [
                'text'     => 'NULL',
                'modified' => time(),
            ], 'id=' . (int)$id, false, true);
        } else {
            return $db->delete_query('dvz_shoutbox', 'id=' . (int)$id);
        }
    }

    /**
     * Wysyla wiadomosc z konta bota
     *
     * @param string $message Message
     *
     * @return int
     */
    public static function shout(string $message, int $targetId=0, int $toDeleteId=0): int
    {
        global $mybb, $db, $plugins;
        $data = [
            'uid' => $mybb->settings['dvz_sb_bot_id'],
            'text' => $message,
            'ipaddress' => $db->escape_binary(my_inet_pton(get_ip())),
            'date' => TIME_NOW,
        ];

        if ($targetId > 0) {
            $data['text'] .= "[uid={$targetId}]";
        }

        foreach ($data as $key => &$value) {
            if (!in_array($key, array_keys($mybb->binary_fields['dvz_shoutbox']))) {
                $value = $db->escape_string($value);
            }
        }

        $plugins->run_hooks('dvz_shoutbox_bot_shout', $data);

        if ($toDeleteId > 0) {
            static::deleteShout($toDeleteId);
        }

        return $db->insert_query('dvz_shoutbox', $data);
    }
}
