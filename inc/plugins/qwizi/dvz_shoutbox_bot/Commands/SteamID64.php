<?php

class Qwizi_DVZSB_Commands_SteamID64 extends Qwizi_DVZSB_Commands_Base
{
    private function getCommunityFromID($id)
    {
        $accountarray = explode(":", $id);
        $idnum = $accountarray[1];
        $accountnum = $accountarray[2];
        $constant = '76561197960265728';
        $number = bcadd(bcmul($accountnum, 2), bcadd($idnum, $constant)); // ($accountnum *2) + ($idnum + $constant)
        return $number;
    }

    public function convert($user, $target)
    {
        $error = [];
        if (isset($target) && !empty($target)) {
            if (strpos($target, 'STEAM') === false) {
                $error['msg'] = "Wystąpił'problem";
            } else {
                $steamid = $this->getCommunityFromID($target);
                $message = "@\"{$user['username']}\" - SteamID64 -> {$steamid}";
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
            $target = $matches[1];

            if (isset($target) && !empty($target)) {
                if (strpos($target, 'STEAM') === false) {
                    $this->error = "Wystąpił'problem";
                } else {
                    $steamid = $this->getCommunityFromID($target);
                }

            } else {
                $this->error = "Wystąpił'problem";
            }

            $this->message = "SteamID64 -> {$steamid}";

            $this->shout();
        }
    }
}
