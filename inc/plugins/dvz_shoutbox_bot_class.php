<?php
define("IN_MYBB", 1);

class dvz_sb_bot
{
   public static $init = null;
   public $mybb;
   public $db;
   public $lang;
   public $bot_id;

   private function __construct()
   {
      global $mybb, $db, $lang;
      $this->mybb =& $mybb;
      $this->db =& $db;
      $this->lang =& $lang;
      if($this->is_installed())
      {
         $this->bot_id = $this->mybb->settings['dvz_shoutbox_bot_id'];
      }
   }

   public static function init()
   {
      if(!isset(self::$init))
      {
         self::$init = new dvz_shoutbox_bot();
      }
      return self::$init;
   }

   public function shout($text)
   {
      $data = [
         'uid' => $this->bot_id,
         'text' => $text,
         'date' => TIME_NOW
      ];
      foreach ($data as $key => &$value)
      {
          if (!in_array($key, array_keys($this->mybb->binary_fields['dvz_shoutbox'])))
          {
             $value = $this->db->escape_string($value);
         }
      }
     return $this->db->insert_query('dvz_shoutbox', $data);
   }

   public function addPanelMessage($string, $answer)
   {
      $data['string'] = $this->db->escape_string($string);
      $data['answer'] = $this->db->escape_string($answer);
      return $this->db->insert_query('dvz_shoutbox_bot', $data);
   }

   public function is_installed()
   {
      return $this->mybb->settings['dvz_shoutbox_bot_on'] !== null;
   }

   public function install()
   {
      $this->uninstall();
      $this->lang->load('dvz_shoutbox_bot');

      $max_disporder = $this->db->fetch_field($this->db->simple_select('settinggroups', 'MAX(disporder) AS max_disporder'), 'max_disporder');

      $group = [
         'gid'             => 'NULL',
         'name'         => 'dvz_shoutbox_bot',
         'title'            => $this->db->escape_string($this->lang->bot_title),
         'description'  => $this->db->escape_string($this->lang->bot_setting_desc),
         'disporder'     => $max_disporder + 1,
         'isdefault'       => '0',
      ];
      $this->db->insert_query('settinggroups', $group);
      $gid = $this->db->insert_id();

      $settings  = [
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_on',
            'title'             => $this->db->escape_string($this->lang->bot_onoff_title),
            'description'  => $this->db->escape_string($this->lang->bot_onoff_desc),
            'optionscode'   => 'onoff',
            'value'           => '1',
            'disporder'     => '1',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_link_on',
            'title'             => $this->db->escape_string($this->lang->bot_link_title),
            'description'  => $this->db->escape_string($this->lang->bot_link_desc),
            'optionscode'   => 'yesno',
            'value'           => 'yes',
            'disporder'     => '2',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_action_on',
            'title'             => $this->db->escape_string($this->lang->bot_action_title),
            'description'  => $this->db->escape_string($this->lang->bot_action_desc),
            'optionscode'   => 'yesno',
            'value'           => 'yes',
            'disporder'     => '3',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_id',
            'title'             => $this->db->escape_string($this->lang->bot_id_title),
            'description'  => $this->db->escape_string($this->lang->bot_id_desc),
            'optionscode'   => 'numeric',
            'value'           => '1',
            'disporder'     => '4',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_register_on',
            'title'             => $this->db->escape_string($this->lang->bot_register_title),
            'description'  => $this->db->escape_string($this->lang->bot_register_desc),
            'optionscode'   => 'yesno',
            'value'           => 'yes',
            'disporder'     => '5',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_register_message',
            'title'             => $this->db->escape_string($this->lang->bot_register_message_title),
            'description'  => $this->db->escape_string($this->lang->bot_register_message_desc),
            'optionscode'   => 'textarea',
            'value'           => $this->lang->bot_register_message_example,
            'disporder'     => '6',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_thread_on',
            'title'             => $this->db->escape_string($this->lang->bot_thread_title),
            'description'  => $this->db->escape_string($this->lang->bot_thread_desc),
            'optionscode'   => 'yesno',
            'value'           => 'yes',
            'disporder'     => '7',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_ignore',
            'title'             => $this->db->escape_string($this->lang->bot_ignore_title),
            'description'  => $this->db->escape_string($this->lang->bot_ignore_desc),
            'optionscode'   => 'forumselect',
            'value'           => '',
            'disporder'     => '8',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_thread_message',
            'title'             => $this->db->escape_string($this->lang->bot_thread_message_title),
            'description'  => $this->db->escape_string($this->lang->bot_thread_message_desc),
            'optionscode'   => 'textarea',
            'value'           =>$this->lang->bot_thread_message_example,
            'disporder'     => '9',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_post_on',
            'title'             => $this->db->escape_string($this->lang->bot_post_title),
            'description'  => $this->db->escape_string($this->lang->bot_post_desc),
            'optionscode'   => 'yesno',
            'value'           => 'yes',
            'disporder'     => '10',
            'gid'              => intval($gid),
         ],
         [
            'sid'             => 'NULL',
            'name'         => 'dvz_shoutbox_bot_post_message',
            'title'             => $this->db->escape_string($this->lang->bot_post_message_title),
            'description'  => $this->db->escape_string($this->lang->bot_post_message_desc),
            'optionscode'   => 'textarea',
            'value'           => $this->lang->bot_post_message_example,
            'disporder'     => '11',
            'gid'              => intval($gid),
         ],
      ];
      $this->db->insert_query_multiple("settings", $settings);
      rebuild_settings();

      if(!$this->db->table_exists('dvz_shoutbox_bot'))
      {
         $this->db->write_query('CREATE TABLE `'.TABLE_PREFIX.'dvz_shoutbox_bot` ( `id` INT NOT NULL AUTO_INCREMENT , `string` TEXT NOT NULL , `answer` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci'.$this->db->build_create_table_collation().';');
      }

      if($this->db->table_exists('dvz_shoutbox_bot'))
      {
         $this->addPanelMessage('Cześć', 'Witaj kolego');
      }
   }

   public function uninstall()
   {
      $this->db->delete_query('settinggroups', 'name=\'dvz_shoutbox_bot\'');
      $this->db->delete_query('settings', 'name LIKE \'dvz_shoutbox_bot%\'');
      if($this->db->table_exists('dvz_shoutbox_bot'))
      {
         $this->db->drop_table('dvz_shoutbox_bot');
      }
      rebuild_settings();
   }
}
 ?>
