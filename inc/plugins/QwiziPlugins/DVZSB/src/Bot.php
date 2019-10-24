<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use Mybb;
use DB_Base;
use MyLanguage;
use PluginSystem;
class Bot
{
    const TABLE_NAME = 'dvz_shoutbox';
    const SETTINGS_NAME = 'dvz_sb_bot';

    /** @var Bot */
    private static $instance = null;

    /** @var MyBB */
    private $mybb;

    /** @var DB_Base */
    private $db;

    /** @var MyLanguage */
    private $lang;

    /** @var PluginSystem */
    private $plugins;

    /** @var string */
    private $message;

    public function __construct(Mybb $mybb, DB_Base $db, MyLanguage $lang, PluginSystem $plugins)
    {
        $this->mybb = $mybb;
        $this->db = $db;
        $this->lang = $lang;
        $this->plugins = $plugins;
    }
    
    /**
     * Create instance of the bot manager
     *
     * @param Mybb $mybb MyBB base object
     * @param DB_Base $db MyBB database object
     * @param MyLanguage $lang Mybb language object
     * @param PluginSystem $plugins Mybb pluginsystem object
     *
     * @return BotManager The created instance
     */
    public static function createInstance(Mybb $mybb, DB_BASE $db, MyLanguage $lang, PluginSystem $plugins)
    {
        if (static::$instance === null) {
            static::$instance = new self($mybb, $db, $lang, $plugins);
        }
        return static::$instance;
    }

    /**
     * Get a prior created bot manager instance
     *
     * @return bool|BotManager The prior created
     *                              instance, or false if
     *                              not created
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            return false;
        }
        return static::$instance;
    }

    /**
     * Short method getInstance
     *
     * @return bool|CommandManager The prior created
     *                              instance, or false if
     *                              not created
     */
    public static function i()
    {
        return static::getInstance();
    }

    /**
     * Set the current bot message
     *
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function settings(string $setting): string
    {
        return $this->mybb->settings[self::SETTINGS_NAME . '_' . $setting];
    }

    /* public function delete($where = "")
    {
        if ($this->mybb->settings['dvz_sb_sync']) {
            $this->update([
                'text' => 'NULL',
                'modified' => time(),
            ], $where, false, true);
        } else {
            return $this->db->delete_query($this->getTableName(), $where);
        }
    } */

    /**
     * Insert shout via bot
     * 
     * @param string $message Message
     * 
     * @return int
     */
    public function shout(string $message): int
    {
        $data = [
                'uid' => $this->settings('id'),
                'text' => $message,
                'ipaddress' => $this->db->escape_binary(my_inet_pton(get_ip())),
                'date' => TIME_NOW,
            ];
        foreach ($data as $key => &$value) {
            if (!in_array($key, array_keys($this->mybb->binary_fields['dvz_shoutbox']))) {
                $value = $this->db->escape_string($value);
            }
        }
            
        return $this->db->insert_query(self::TABLE_NAME, $data);
    }

    /**
     * Convert link
     * 
     * @param string $url
     * @param string $title
     * 
     * @return string
     */
    public function createLink(string $url, string $title): string
    {
        $title = htmlspecialchars_uni($title);
        return "[url=" . $this->mybb->settings['bburl'] . "/" . $url . "]" . $title . "[/url]";
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
}
