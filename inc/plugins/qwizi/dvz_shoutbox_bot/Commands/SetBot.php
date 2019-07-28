<?php

class Qwizi_DVZSB_Commands_SetBot implements Qwizi_DVZSB_Commands_Base
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

    private function setBot($user, $target)
    {
        $error = [];
        $mybb = $this->bot->getMybb();
        $db = $this->bot->getDB();

        if (empty($target)) {
            $error['msg'] = "Nie znaleziono użytkownika";
        } else {
            $db->update_query('settings', ['value' => $db->escape_string((int)$target['uid'])], "name='dvz_sb_bot_id'");
            $this->bot->rebuildSettings();
        }

        if (empty($error)) {
            $this->bot->shout("@\"{$user['username']}\" zmienił konto bota na @\"{$target['username']}\"");
        } else {
            $this->bot->shout($error['msg']);
        }
    }

    public function doAction($data)
    {
        if ($this->bot->accessMod()) {
            if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote($data['command']) . '[\s]+(.*)$/', $data['text'], $matches)) {
                $user = $this->bot->getUserInfoFromUid($data['uid']);
                $target = $this->bot->getUserInfoFromUsername($matches[1]);

                $this->setBot($user, $target, false);
            }
        }
    }
}
