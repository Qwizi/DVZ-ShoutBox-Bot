<?php

class Qwizi_DVZSB_Commands_Prune implements Qwizi_DVZSB_Commands_Base
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

    private function prune($user=null, $target=null, $all=false)
    {
        if ($all == true) {
            $this->bot->delete();
        } else {
            $this->bot->delete("uid={$target['uid']}");
            $this->bot->shout("@\"{$user['username']}\" usunął wiadomości użytkownika @\"{$target['username']}\"");;
        }
    }


    public function doAction($data)
    {
        if ($this->bot->accessMod()) {
            if ($data['text'] == $this->bot->settings('commands_prefix') . 'prune') {
                $this->prune(null, null, true);
            }

            if (preg_match('/^\\' . $this->bot->settings('commands_prefix') . preg_quote('prune') . '[\s]+(.*)$/', $data['text'], $matches)) {
                $user = $this->bot->getUserInfoFromUid($data['uid']);
                $target = $this->bot->getUserInfoFromUsername($matches[1]);

                $this->bot->delete("id={$data['shout_id']}");

                $this->prune($user, $target, false);
            }
        }
    }
}
