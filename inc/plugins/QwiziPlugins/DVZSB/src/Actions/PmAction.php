<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

class PMAction
{
    static function send(string $subject, string $message, int $touid) {
        global $mybb;
        $fromid = $mybb->settings['dvz_sb_bot_id'];
        $private_msg = [
            'subject' => $subject,
            'message' => $message,
            'touid' => $touid
        ];
        \send_pm($private_msg, $fromid, true);
    }
}