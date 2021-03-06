<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use DB_Base;
use MyBB;
use MyLanguage;
use pluginSystem;

class Bot
{
    private static $instance = null;
    private $mybb;
    private $db;
    private $lang;
    private $plugins;
    private $tableName = 'dvz_shoutbox';
    private $settingsGroupName = 'dvz_sb_bot';
    private $botID;
    private $message;

    public function __construct(Mybb $mybb, DB_Base $db, MyLanguage $lang, PluginSystem $plugins)
    {
        $this->mybb = $mybb;
        $this->db = $db;
        $this->lang = $lang;
        $this->plugins = $plugins;
        $this->botID = (int) $this->mybb->settings['dvz_sb_bot_id'];
    }

    public static function createInstance(Mybb $mybb, DB_BASE $db, MyLanguage $lang, PluginSystem $plugins)
    {
        if (static::$instance === null) {
            static::$instance = new self($mybb, $db, $lang, $plugins);
        }
        return static::$instance;
    }

    public static function i()
    {
        if (static::$instance === null) {
            return false;
        }
        return static::$instance;
    }

    public function getDB()
    {
        return $this->db;
    }

    public function getMybb()
    {
        return $this->mybb;
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getBotID(): int
    {
        return $this->botID;
    }

    public function getPlugins()
    {
        return $this->plugins;
    }

    public function getSettingsGroupName(): string
    {
        return $this->settingsGroupName;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function settings(string $setting): string
    {
        return $this->mybb->settings[$this->getSettingsGroupName() . '_' . $setting];
    }

    public function create($data)
    {
        return $this->db->insert_query($this->getTableName(), $data);
    }

    public function update($updateArray, $where, $limit, $cos)
    {
        return $this->db->update_query($this->getTableName(), $updateArray, $where, $limit, $cos);
    }

    public function delete($where = "")
    {
        if ($this->mybb->settings['dvz_sb_sync']) {
            $this->update([
                'text' => 'NULL',
                'modified' => time(),
            ], $where, false, true);
        } else {
            return $this->db->delete_query($this->getTableName(), $where);
        }
    }

    public function shout(string $message)
    {
        $data = [
            'uid' => $this->getBotID(),
            'text' => $message,
            'ipaddress' => $this->db->escape_binary(my_inet_pton(get_ip())),
            'date' => TIME_NOW,
        ];
        foreach ($data as $key => &$value) {
            if (!in_array($key, array_keys($this->mybb->binary_fields['dvz_shoutbox']))) {
                $value = $this->db->escape_string($value);
            }
        }
        return $this->create($data);
    }

    public function createLink(string $url, string $title): string
    {
        $title = htmlspecialchars_uni($title);
        $link = "[url=" . $this->mybb->settings['bburl'] . "/" . $url . "]" . $title . "[/url]";
        return $link;
    }

    public function convert(string $action, array $dataArray)

    {
        $message = $this->settings($action . '_message');

        if (is_array($dataArray) && !empty($dataArray)) {
            foreach ($dataArray as $key => $value) {
                $message = str_replace('{' . $key . '}', $value, $message);
            }
        }

        $this->setMessage($message);

        return $this;
    }

    public function accessMod()
    {
        $array = explode(",", $this->mybb->settings['dvz_sb_groups_mod']);

        return (
            ($array[0] == -1 || is_member($array)) || ($this->mybb->settings['dvz_sb_supermods'] && $this->mybb->usergroup['issupermod']));
    }


    public function user_last_shout_time($uid, $matches)
    {
        return $this->db->fetch_field(
            $this->db->simple_select('dvz_shoutbox s', 'date', 'uid=' . (int) $uid . ' AND s.text REGEXP "' . $matches . '"', [
                'order_by'  => 'date',
                'order_dir' => 'desc',
                'limit'     => 1,
            ]),
            'date'
        );
    }

    public function antiflood_pass($matches)
    {
        return ((TIME_NOW - $this->user_last_shout_time($this->mybb->user['uid'], $matches)) > $this->settings['dvz_sb_antiflood']);
    }
}
