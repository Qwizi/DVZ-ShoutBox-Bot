<?php
declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Exceptions\ApplicationException;
use Qwizi\DVZSB\Exceptions\UserNotFoundException;

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
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            try {
                $target = $this->bot->getUserInfoFromUsername($matches[1]);

                if (empty($target)) {
                    throw new UserNotFoundException($lang->bot_steamid64_error);
                }
                if (strpos($target, 'STEAM') === false) {
                    throw new ApplicationException($lang->bot_steamid64_error);
                }
                
                $steamid = $this->getCommunityFromID($target);

            } catch (ApplicationException $e) {
                $this->error = $e->getMessage();
            }

            $this->message = "SteamID64 -> {$steamid}";
            
            $this->shout();
        }
    }
}
