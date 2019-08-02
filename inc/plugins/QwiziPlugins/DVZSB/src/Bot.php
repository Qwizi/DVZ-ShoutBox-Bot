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
    private $PL;
    private $tableName = 'dvz_shoutbox';
    private $settingsGroupName = 'dvz_sb_bot';
    private $botID;

    public function __construct(Mybb $mybb, DB_Base $db, MyLanguage $lang, PluginSystem $plugins, $PL)
    {
        $this->mybb = $mybb;
        $this->db = $db;
        $this->lang = $lang;
        $this->plugins = $plugins;
        $this->PL = $PL;
        $this->botID = (int) $this->mybb->settings['dvz_sb_bot_id'];
    }

    public static function createInstance(Mybb $mybb, DB_BASE $db, MyLanguage $lang, PluginSystem $plugins, $PL)
    {
        if (static::$instance === null) {
            static::$instance = new self($mybb, $db, $lang, $plugins, $PL);
        }
        return static::$instance;
    }

    public static function getInstance()
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

    public function getPL()
    {
        return $this->PL;
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

    public function settings(string $setting): string
    {
        $mybb = $this->getMybb();
        return $mybb->settings[$this->getSettingsGroupName() . '_' . $setting];
    }

    public function get(bool $many = false, string $fields, string $where, array $optionsArray): array
    {
        $data = [];
        if ($many == false) {
            $data = $this->db->fetch_array($this->db->simple_select($this->getTableName(), $fields, $where, $optionsArray));
        } else {
            $query = $this->db->simple_select($this->getTableName(), $fields, $where, $optionsArray);

            if ($this->db->num_rows($query)) {
                while ($row = $this->db->fetch_array($query)) {
                    $data[] = $row;
                }
            }
        }

        return $data;
    }

    public function create($data)
    {
        return $this->db->insert_query($this->getTableName(), $data);
    }

    public function delete($where = "")
    {
        if ($this->mybb->settings['dvz_sb_sync']) {
            $result = $this->db->update_query('dvz_shoutbox', [
                'text' => 'NULL',
                'modified' => time(),
            ], $where, false, true);

            if ($result['modified']) {
                return $this->db->delete_query($this->getTableName(), $where);
            }
        } else {
            return $this->db->delete_query($this->getTableName(), $where);
        }
    }

    public function update($updateArray, $where, $limit)
    {
        return $this->db->update_query($this->getTableName(), $updateArray, $where);
    }

    public function shout(string $message)
    {
        $db = $this->getDB();
        $mybb = $this->getMybb();
        $data = [
            'uid' => $this->getBotID(),
            'text' => $message,
            'ipaddress' => $db->escape_binary(my_inet_pton(get_ip())),
            'date' => TIME_NOW,
        ];
        foreach ($data as $key => &$value) {
            if (!in_array($key, array_keys($mybb->binary_fields['dvz_shoutbox']))) {
                $value = $db->escape_string($value);
            }
        }
        return $this->create($data);
    }

    public function isMember($groups, $user = false)
    {
        $mybb = $this->getMybb();
        if (empty($groups)) {
            return array();
        }
        if ($user == false) {
            $user = $mybb->user;
        } else if (!is_array($user)) {
            // Assume it's a UID
            $user = get_user($user);
        }
        $memberships = array_map('intval', explode(',', $user['additionalgroups']));
        $memberships[] = $user['usergroup'];
        if (!is_array($groups)) {
            if ((int) $groups == -1) {
                return $memberships;
            } else {
                if (is_string($groups)) {
                    $groups = explode(',', $groups);
                } else {
                    $groups = (array) $groups;
                }
            }
        }
        $groups = array_filter(array_map('intval', $groups));
        return array_intersect($groups, $memberships);
    }

    public function accessMod()
    {
        $array = explode(",", $this->mybb->settings['dvz_sb_groups_mod']);

        return (
            ($array[0] == -1 || $this->isMember($array)) ||
            ($this->mybb->settings['dvz_sb_supermods'] && $this->mybb->usergroup['issupermod'])
        );
    }

    public function userLastShoutTime($uid)
    {
        $db = $this->getDB();
        return $db->fetch_field(
            $db->simple_select('dvz_shoutbox s', 'date', 'uid=' . (int) $uid . ' AND s.text IS NOT NULL', [
                'order_by' => 'date',
                'order_dir' => 'desc',
                'limit' => 1,
            ]),
            'date'
        );
    }

    public function antiflood()
    {
        $mybb = $this->getMybb();
        return (
            !$mybb->settings['dvz_sb_antiflood'] ||
            (TIME_NOW - $this->userLastShoutTime($mybb->user['uid'])) > $mybb->settings['dvz_sb_antiflood']
        );
    }

    public function rebuildSettings()
    {
        $db = $this->getDB();
        $mybb = $this->getMybb();
        $query = $db->simple_select("settings", "value, name", "", array(
            'order_by' => 'title',
            'order_dir' => 'ASC',
        ));
        $settings = '';
        while ($setting = $db->fetch_array($query)) {
            $mybb->settings[$setting['name']] = $setting['value'];
            $setting['value'] = addcslashes($setting['value'], '\\"$');
            $settings .= "\$settings['{$setting['name']}'] = \"{$setting['value']}\";\n";
        }
        $settings = "<" . "?php\n/*********************************\ \n  DO NOT EDIT THIS FILE, PLEASE USE\n  THE SETTINGS EDITOR\n\*********************************/\n\n$settings\n";
        file_put_contents(MYBB_ROOT . 'inc/settings.php', $settings, LOCK_EX);
        $GLOBALS['settings'] = &$mybb->settings;
    }

    public function createLink(string $url, string $title): string
    {
        $mybb = $this->getMybb();
        $db = $this->getDB();
        $title = htmlspecialchars_uni($title);
        $link = "[url=" . $mybb->settings['bburl'] . "/" . $url . "]" . $title . "[/url]";
        return $link;
    }

    public function createMsg(string $action, array $data): string
    {
        $db = $this->getDB();

        $message = $this->settings($action . '_message');
        $db->escape_string(htmlspecialchars_uni($message));
        if (is_array($data)) {
            if (!empty($data['username'])) {
                $message = str_replace('{username}', $data['username'], $message);
            }

            if (!empty($data['subject'])) {
                $message = str_replace('{subject}', $data['subject'], $message);
            }

            if (!empty($data['forum'])) {
                $message = str_replace('{forum}', $data['forum'], $message);
            }

            if (!empty($data['message'])) {
                $message = str_replace('{message}', $data['message'], $message);
                $message = str_replace('{pid}', $data['pid'], $message);
                $message = str_replace('{dateline}', $data['dateline'], $message);
            }
        }
        return $message;
    }

    public function getUserInfoFromUsername($username)
    {
        $db = $this->getDB();
        return $db->fetch_array($db->simple_select('users', "*", 'username="' . $username . '"'));
    }

    public function getUserInfoFromUid($uid)
    {
        $db = $this->getDB();
        $user = $db->fetch_array($db->simple_select('users', "*", 'uid="' . $uid . '"'));
        return $user;
    }
}
