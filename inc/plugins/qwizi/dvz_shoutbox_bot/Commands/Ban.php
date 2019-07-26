<?php

class Qwizi_DVZSB_Commands_Ban implements Qwizi_DVZSB_Commands_Base
{
    private $bot;

    public function __construct(Qwizi_DVZSB_Bot $bot)
    {
        $this->bot = $bot;
    }

    public function doAction($text, $uid)
    {
        if ($this->bot->accessMod()) {
            if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote('ban') . '[\s]+(.*)$/', $text, $matches)) {
                $user = $this->bot->getUserInfoFromUid($uid);
                $target = $this->bot->getUserInfoFromUsername($matches[1]);
                $errMsg = $this->bot->banUser($target['uid']);

                if ($errMsg == '') {
                    return $this->bot->shout("@\"{$user['username']}\" zbanował użytkownika @\"{$target['username']}\"");
                } else {
                    return $this->bot->shout($errMsg);
                }
            }
        }
    }
}
