<?php

class Qwizi_DVZSB_Commands_Prune implements Qwizi_DVZSB_Commands_Base
{
    private $bot;

    public function __construct(Qwizi_DVZSB_Bot $bot)
    {
        $this->bot = $bot;
    }

    public function doAction($text, $uid)
    {
        if ($text == $this->bot->settings('commands_prefix').'prune') {
            $this->bot->delete();

        }

        if (preg_match('/^\\'. $this->bot->settings('commands_prefix') . preg_quote('prune') . '[\s]+(.*)$/', $text, $matches)) {
            $user = $this->bot->getUserInfo($matches[1]);
            $this->bot->delete("uid={$user['uid']}");
            return $this->bot->shout("Usunięto wiadomości użytkownika {$user['username']}");
        }
    }
}