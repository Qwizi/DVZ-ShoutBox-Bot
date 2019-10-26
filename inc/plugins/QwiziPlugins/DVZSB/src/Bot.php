<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

use Mybb;
use DB_Base;

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

    private function __construct(Mybb $mybb, DB_Base $db)
    {
        $this->mybb = $mybb;
        $this->db = $db;
    }
    
    /**
     * Create instance of the bot manager
     *
     * @param Mybb $mybb MyBB base object
     * @param DB_Base $db MyBB database object
     *
     * @return BotManager The created instance
     */
    public static function createInstance(Mybb $mybb, DB_BASE $db)
    {
        if (static::$instance === null) {
            static::$instance = new self($mybb, $db);
        }
        return static::$instance;
    }

    /**
     * Get a prior created bot manager instance
     *
     * @return bool|Bot The prior created
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
     * @return bool|Bot The prior created
     *                              instance, or false if
     *                              not created
     */
    public static function i()
    {
        return static::getInstance();
    }

    public function settings(string $setting): string
    {
        return $this->mybb->settings[self::SETTINGS_NAME . '_' . $setting];
    }

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

    public function accessMod()
    {
        $array = explode(",", $this->mybb->settings['dvz_sb_groups_mod']);

        return (
            ($array[0] == -1 || is_member($array)) || ($this->mybb->settings['dvz_sb_supermods'] && $this->mybb->usergroup['issupermod']));
    }
}
