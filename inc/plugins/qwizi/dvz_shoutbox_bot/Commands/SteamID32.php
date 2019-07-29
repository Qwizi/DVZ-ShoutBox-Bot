<?php

class Qwizi_DVZSB_Commands_SteamID32 extends Qwizi_DVZSB_Commands_Base
{
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

    public function doAction($data)
    {
        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $target = $matches[1];

            if (isset($target) && !empty($target)) {
                if (strpos($target, 'STEAM') === false) {
                    $steamid = $this->getIDFromCommunity($target);
                } else {
                    $this->error = "Wystąpił'problem";
                }

            } else {
                $this->error = "Wystąpił'problem";
            }

            $this->message = "SteamID32 -> {$steamid}";

            $this->shout();
        }
    }
}
