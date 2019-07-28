<?php

class Qwizi_DVZSB_Commands_Help implements Qwizi_DVZSB_Commands_Base
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

    public function help()
    {
        $error = [];
        // $mybb = $this->bot->getMybb();
        $PL = $this->bot->getPL();

        $commandPrefix = $this->bot->settings('commands_prefix');

        $pluginCache = $PL->cache_read('dvz_shoutbox_bot');

        $commandsArray = $pluginCache['commands'];

        if (!empty($commandsArray)) {
            $command = '';
            for ($i = 0; $i < count($commandsArray); $i++) {
                // [quote="{username}" pid="{pid}" dateline="{dateline}"]{message}[/quote]
                $command .= "{$commandPrefix}{$commandsArray[$i]['command']} - {$commandsArray[$i]['description']}\n";
            }
        } else {
            $error['msg'] = "Wystąpił problem.";
        }

        if (empty($error)) {
            $this->bot->shout("{$command}");
        } else {
            return $this->bot->shout($error['msg']);
        }
    }

    public function doAction($data)
    {
        if ($data['text'] == $this->bot->settings('commands_prefix') . $data['command']) {
            //$user = $this->bot->getUserInfoFromUid($data['uid']);

            // Ban user
            $this->help();
        }

    }
}
