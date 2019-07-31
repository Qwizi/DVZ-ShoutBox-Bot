<?php
declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

class SteamID64 extends Base
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

    public function doAction(array $data): void
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
