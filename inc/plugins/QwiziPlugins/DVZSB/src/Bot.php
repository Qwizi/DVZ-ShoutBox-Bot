<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;


class Bot
{
    /**
     * Wysyla wiadomosc z konta bota
     *
     * @param string $message Message
     *
     * @return int
     */
    public static function shout(string $message, int $targetId=0): int
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

        return $db->insert_query('dvz_shoutbox', $data);
    }
}
