<?php
declare(strict_types=1);

namespace Qwizi\DVZSB\Commands;

use Qwizi\DVZSB\Exceptions\ApplicationException;
use Qwizi\DVZSB\Exceptions\UserNotFoundException;

class SteamID32 extends Base
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

    public function doAction(array $data): void
    {
        if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
            $plugins = $this->bot->getPlugins();
            $lang = $this->bot->getLang();

            $lang->load('dvz_shoutbox_bot');

            try {
                $target = $this->bot->getUserInfoFromUsername($matches[1]);

                if (empty($target)) {
                    throw new UserNotFoundException($lang->bot_steamid32_error);
                }
                if (strpos($target, 'STEAM') === true) {
                    throw new ApplicationException($lang->bot_steamid32_error);
                }
                
                $steamid = $this->getIDFromCommunity($target);

            } catch (ApplicationException $e) {
                $this->error = $e->getMessage();
            }

            $this->message = "SteamID32 -> {$steamid}";
            
            $this->shout();

            $this->returned_value = [
                'tuid' => $target['uid'],
                'message' => $this->message,
                'error' => $this->error
            ];

            $plugins->run_hooks("dvz_shoutbox_bot_commands_steamid32_commit", $this->returned_value);
        }
    }
}
