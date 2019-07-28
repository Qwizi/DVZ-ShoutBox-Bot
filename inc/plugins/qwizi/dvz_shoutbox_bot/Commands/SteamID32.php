<?php

class Qwizi_DVZSB_Commands_SteamID32 implements Qwizi_DVZSB_Commands_Base
{
    private $bot;

    public function __construct(Qwizi_DVZSB_Bot $bot)
    {
        $this->bot = $bot;
    }

    public function getBot()
    {
        return $this->bot;
    }

    private function getIDFromCommunity($id)
    {
        $idnum = '0';
        $accnum = '0';
        $constant = '76561197960265728';
        if (bcmod($id, '2') == 0) {
            $idnum = '0';
            $temp = bcsub($id, $constant);
        } else {
            $idnum = '1';
            $temp = bcsub($id, bcadd($constant, '1'));
        }
        $accnum = bcdiv($temp, '2');
        return "STEAM_0:" . $idnum . ":" . number_format($accnum, 0, '', '');
    }

    public function convert($user, $target)
    {
        $error = [];
        if (isset($target) && !empty($target)) {
            if (strpos($target, 'STEAM') === false) {
                $steamid = $this->getIDFromCommunity($target);
                $message = "SteamID32 -> {$steamid}";
            } else {
                $error['msg'] = "Wystąpił'problem";
            }

        } else {
            $error['msg'] = "Wystąpił'problem";
        }

        if (empty($error)) {
            $this->bot->shout($message);
        } else {
            $this->bot->shout($error['msg']);
        }
    }

    public function doAction($data)
    {
        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $user = $this->bot->getUserInfoFromUid($data['uid']);
            $target = $matches[1];

            // Ban user
            $this->convert($user, $target);
        }

    }
}
