<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!defined('IN_MYBB')) {
   die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}
if (!defined("PLUGINLIBRARY")) {
   define("PLUGINLIBRARY", MYBB_ROOT . "inc/plugins/pluginlibrary.php");
}

$plugins->add_hook('member_do_register_end', ['dvz_shoutbox_bot', 'register']);
$plugins->add_hook('datahandler_post_insert_thread_end', ['dvz_shoutbox_bot', 'thread']);
$plugins->add_hook('datahandler_post_insert_post_end', ['dvz_shoutbox_bot', 'post']);
/*$plugins->add_hook('dvz_shoutbox_shout_commit', ['dvz_shoutbox_bot', 'dvz_shoutbox_bot_action']);
 $plugins->add_hook('admin_user_menu', ['dvz_shoutbox_bot', 'dvz_shoutbox_bot_admin_user_menu']);
$plugins->add_hook('admin_user_action_handler', ['dvz_shoutbox_bot', 'dvz_shoutbox_bot_user_action_handler']); */

function dvz_shoutbox_bot_info()
{
   global $lang, $db;
   $lang->load('dvz_shoutbox_bot');

   return [
      'name'            => $db->escape_string($lang->bot_title),
      'description'     => $db->escape_string($lang->bot_desc),
      'author'         => 'Adrian \'Qwizi\' Ciołek',
      'version'         => '1.5',
      'compatibility'   => '18*',
      'codename' => ''
   ];
}

function dvz_shoutbox_bot_is_installed()
{
   global $mybb;
   return $mybb->settings['dvz_sb_bot_onoff'] !== null;
}

function dvz_shoutbox_bot_install()
{
   global $db, $PL, $lang;

   $lang->load('dvz_shoutbox_bot');

   $PL->settings(
      'dvz_sb_bot',
      $lang->bot_title,
      $lang->bot_settings_desc,
      [
         'onoff' =>  [
            'title' => $lang->bot_onoff_title,
            'description' => $lang->bot_onoff_desc,
            'optionscode' => 'onoff',
            'value'  => 1
         ],
         'id' => [
            'title' => $lang->bot_id_title,
            'description' => $lang->bot_id_desc,
            'optionscode'  => 'numeric',
            'value'  => 1
         ],
         'register_onoff' => [
            'title'  => $lang->bot_register_title,
            'description' => $lang->bot_register_desc,
            'optionscode' => 'onoff',
            'value' => 1
         ],
         'register_message' => [
            'title'  => $lang->bot_register_message_title,
            'description' => $lang->bot_register_message_desc,
            'optionscode' => 'textarea',
            'value'  => $lang->bot_register_message_example
         ],
         'thread_onoff' => [
            'title' => $lang->bot_thread_title,
            'description' => $lang->bot_thread_desc,
            'optionscode' => 'onoff',
            'value'  => 1
         ],
         'thread_ignore' => [
            'title' => $lang->bot_ignore_title,
            'description' => $lang->bot_ignore_desc,
            'optionscode' => 'forumselect',
            'value'  => ''
         ],
         'thread_message'  => [
            'title' => $lang->bot_thread_message_title,
            'description' => $lang->bot_thread_message_desc,
            'optionscode' => 'textarea',
            'value'  => $lang->bot_thread_message_example
         ],
         'thread_message' => [
            'title'  => $lang->bot_thread_message_title,
            'description' => $lang->bot_thread_message_desc,
            'optionscode' => 'textarea',
            'value'  => $lang->bot_thread_message_example
         ],
         'post_onoff' => [
            'title' => $lang->bot_post_title,
            'description' => $lang->bot_post_desc,
            'optionscode' => 'onoff',
            'value'  => 1
         ],
         'post_message' => [
            'title'  => $lang->bot_post_message_title,
            'description' => $lang->bot_post_message_desc,
            'optionscode' => 'textarea',
            'value'  => $lang->bot_post_message_example
         ],
      ]
   );

   if (!$db->table_exists('dvz_shoutbox_bot')) {
      $db->write_query('CREATE TABLE `' . TABLE_PREFIX . 'dvz_shoutbox_bot` ( `id` INT NOT NULL AUTO_INCREMENT , `string` TEXT NOT NULL , `answer` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci' . $db->build_create_table_collation() . ';');
   }
}

function dvz_shoutbox_bot_uninstall()
{
   global $db, $PL, $mybb;

   $PL->settings_delete('dvz_sb_bot');

   //Delete settings, if plugin version < 1.5
   if ($mybb->settings['dvz_shoutbox_bot_on'] !== null) {
      $db->delete_query('settinggroups', 'name=\'dvz_shoutbox_bot\'');
      $db->delete_query('settings', 'name LIKE \'dvz_shoutbox_bot%\'');
      rebuild_settings();
   }

   if ($db->table_exists('dvz_shoutbox_bot')) {
      $db->drop_table('dvz_shoutbox_bot');
   }
}

class dvz_shoutbox_bot
{
   static function register()
   {
      global $db, $mybb;

      if (static::is_plugin_active()) {
         if ($mybb->settings['dvz_sb_bot_register_onff']) {
            $user = $db->fetch_array($db->simple_select('users', 'username', '', ['order_by' => 'regdate DESC', 'limit' => 1]));

            $message = static::create_message('register', ['username' => $user['username']]);

            static::shout($message);
         }
      }
   }

   static function thread()
   {
      global $db, $mybb, $new_thread;

      if (static::is_plugin_active()) {
         if ($mybb->settings['dvz_sb_bot_thread_onoff']) {
            $thread = $db->fetch_array($db->simple_select('threads', "tid, fid, username, subject", '', ['order_by' => 'dateline DESC', 'limit' => 1]));

            $forum = get_forum($thread['fid']);

            $threadLink = get_thread_link($forum['tid']);
            $threadTitle = $db->escape_string($thread['subject']);
            $threadFullLink = static::create_link($threadLink, $threadTitle);

            $forumLink = get_forum_link($forum['fid']);
            $forumTitle = $db->escape_string($forum['name']);
            $forumFullLink = static::create_link($forumLink, $forumTitle);

            $message = static::create_message('thread', [
               'username' => $thread['username'],
               'subject' => $threadFullLink,
               'forum'  => $forumFullLink
            ]);

            if (!$new_thread['savedraft']) {
               $forum_ignore = explode(',', $mybb->settings['dvz_sb_bot_thread_ignore']);
               if (!in_array($thread['fid'], $forum_ignore))
                  statc::shout($message);
            }
         }
      }
   }

   static function post()
   {
      global $db, $mybb;

      if (static::is_plugin_active()) {
         if ($mybb->settings['dvz_sb_bot_post_onoff']) {
            $post = $db->fetch_array($db->simple_select('posts', "pid, tid, fid, username, subject", '', ['order_by' => 'dateline DESC', 'limit' => 1]));

            $postLink = get_post_link($post['pid'], $post['tid']) . '#pid' . $post['pid'];
            $postLinkPid = $postLink . '#pid' . $post['pid'];
            $postTitle = $db->escape_string($post['subject']);
            $postFullLink = static::create_link($postLinkPid, $postTitle);

            $message = static::create_message('post', [
               'username' => $post['username'],
               'subject' => $postFullLink
            ]);

            $forum_ignore = explode(',', $mybb->settings['dvz_sb_bot_thread_ignore']);
            if (!in_array($post['fid'], $forum_ignore))
               statc::shout($message);
         }
      }
   }

   static function shout($message)
   {
      global $db, $mybb;

      $data = [
         'uid' => $mybb->settings['dvz_sb_bot_id'],
         'text' => $message,
         'date' => TIME_NOW
      ];
      foreach ($data as $key => &$value) {
         if (!in_array($key, array_keys($this->mybb->binary_fields['dvz_shoutbox']))) {
            $value = $this->db->escape_string($value);
         }
      }
      return $db->insert_query('dvz_shoutbox', $data);
   }

   static function create_link($url, $title)
   {
      global $mybb;

      $link = "[url=" . $mybb->settings['bburl'] . "/" . $url . "]" . $title . "[/url]";
      return $link;
   }

   static function create_message($action, $data)
   {
      global $mybb;

      $message = '';
      if ($action == 'register') {
         $message = $mybb->settings['dvz_sb_bot_register_message'];
         $message = str_replace('{username}', $data['username'], $message);
      } else if ($action == 'thread') {
         $message = $mybb->settings['dvz_sb_bot_thread_message'];
         $message = str_replace('{username}', $data['username'], $message);
         $message = str_replace('{subject}', $data['subject'], $message);
         $message = str_replace('{forum}', $data['forum'], $message);
      } else if ($action == 'post') {
         $message = $mybb->settings['dvz_sb_bot_post_message'];
         $message = str_replace('{username}', $data['username'], $message);
         $message = str_replace('{subject}', $data['subject'], $message);
      }

      return $message;
   }

   static function is_plugin_active()
   {
      global $mybb;
      return $mybb->settings['dvz_sb_bot_onoff'];
   }
}

/* function dvz_shoutbox_bot_admin_user_menu(&$sub_menu)
{
   global $lang;
   $lang->load('dvz_shoutbox_bot');
   $sub_menu[] = ['id' => 'dvz_shoutbox_bot', 'title' => 'DVZ ShoutBox Bot', 'link' => 'index.php?module=user-dvz_shoutbox_bot'];
} */

/* function dvz_shoutbox_bot_user_action_handler(&$actions)
{
   $actions['dvz_shoutbox_bot'] = ['active' => 'dvz_shoutbox_bot', 'file' => 'dvz_shoutbox_bot.php'];
} */

/* function dvz_shoutbox_bot_action_register()
{
   global $db, $mybb;
   if ($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_register_on']) {
      $row = $db->fetch_array($db->simple_select(" users ", " username ", " ", ['order_by' => 'regdate DESC', 'limit' => 1]));
      $bot['message'] = $mybb->settings['dvz_shoutbox_bot_register_message'];
      if ($mybb->settings['dvz_shoutbox_bot_link_on']) {
         $bot['message'] = str_replace('{username}', " @\"" . $row['username'] . "\"", $bot['message']);
      } else {
         $bot['message'] = str_replace('{username}', $row['username'], $bot['message']);
      }
      $dvzBot = dvzBot_init();
      $dvzBot->shout($bot['message']);
   }
} */

/* function dvz_shoutbox_bot_action_thread()
{
   global $db, $mybb, $new_thread;
   if ($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_thread_on']) {
      $row = $db->fetch_array($db->simple_select("threads", "tid, fid, username, subject", "", ['order_by' => 'dateline DESC', 'limit' => 1]));

      $forum = get_forum($row['fid']);
      $bot['message'] = $mybb->settings['dvz_shoutbox_bot_thread_message'];
      $link = '[url=' . $mybb->settings['bburl'] . '/' . get_thread_link($row['tid']) . ']' . $db->escape_string($row['subject']) . '[/url]';
      $link2 = '[url=' . $mybb->settings['bburl'] . '/' . get_forum_link($row['fid']) . ']' . $db->escape_string($forum['name']) . '[/url]';
      $bot['message'] = str_replace('{subject}', $link, $bot['message']);
      $bot['message'] = str_replace('{forum}', $link2, $bot['message']);
      if ($mybb->settings['dvz_shoutbox_bot_link_on']) {
         $bot['message'] = str_replace('{username}', "@\"" . $row['username'] . "\"", $bot['message']);
      } else {
         $bot['message'] = str_replace('{username}', $row['username'], $bot['message']);
      }
      if (!$new_thread['savedraft']) {
         $ignore = explode(',', $mybb->settings['dvz_shoutbox_bot_ignore']);
         if (!in_array($row['fid'], $ignore)) {
            $dvzBot = dvzBot_init();
            $dvzBot->shout($bot['message']);
         }
      }
   }
} */

/* function dvz_shoutbox_bot_action_post()
{
   global $db, $mybb;
   if ($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_post_on']) {
      $row = $db->fetch_array($db->simple_select("posts", "pid, tid, fid, username, subject", "", ['order_by' => 'dateline DESC', 'limit' => 1]));

      $link = '[url=' . $mybb->settings['bburl'] . '/' . get_post_link($row['pid'], $row['tid']) . '#pid' . $row['pid'] . ']' . $row['subject'] . '[/url]';
      $bot['message'] = $mybb->settings['dvz_shoutbox_bot_post_message'];
      $bot['message'] = str_replace('{subject}', $link, $bot['message']);
      if ($mybb->settings['dvz_shoutbox_bot_link_on']) {
         $bot['message'] = str_replace('{username}', "@\"" . $row['username'] . "\"", $bot['message']);
      } else {
         $bot['message'] = str_replace('{username}', $row['username'], $bot['message']);
      }
      $ignore = explode(',', $mybb->settings['dvz_shoutbox_bot_ignore']);
      if (!in_array($row['fid'], $ignore)) {
         $dvzBot = dvzBot_init();
         $dvzBot->shout($bot['message']);
      }
   }
}

function dvz_shoutbox_bot_action(&$data)
{
   global $db, $mybb;
   if ($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_action_on']) {
      $data['text'] = $db->escape_string($data['text']);
      $row = $db->fetch_array($db->simple_select("dvz_shoutbox_bot", "string, answer", "string='{$data['text']}'"));
      if ($data['text'] === $row['string']) {
         $dvzBot = dvzBot_init();
         $dvzBot->shout($row['answer']);
      }
   }
} */
