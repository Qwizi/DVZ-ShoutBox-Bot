<?php
if(!defined('IN_MYBB'))
{
   die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

$plugins->add_hook('member_do_register_end', 'dvz_shoutbox_bot_action_register');
$plugins->add_hook('datahandler_post_insert_thread_end', 'dvz_shoutbox_bot_action_thread');
$plugins->add_hook('datahandler_post_insert_post_end', 'dvz_shoutbox_bot_action_post');
$plugins->add_hook('dvz_shoutbox_shout_commit', 'dvz_shoutbox_bot_action');
$plugins->add_hook('admin_user_menu', 'dvz_shoutbox_bot_admin_user_menu');
$plugins->add_hook('admin_user_action_handler', 'dvz_shoutbox_bot_user_action_handler');

function dvz_shoutbox_bot_info()
{
    global $lang, $db;
    $lang->load('dvz_shoutbox_bot');

   return [
      'name'            => $db->escape_string($lang->bot_title),
      'description'     => $db->escape_string($lang->bot_desc),
      'website'         => 'http://sharkservers.eu',
      'author'         => 'Adrian \'Qwizi\' Ciołek',
      'authorsite'    => 'http://sharkservers.eu',
      'version'         => '1.4',
      'codename'     => '',
      'compatibility'   => '18*',
   ];
}

function dvzBot_init()
{
   require_once MYBB_ROOT.'inc/plugins/dvz_shoutbox_bot_class.php';
   return dvz_shoutbox_bot::init();
}

function dvz_shoutbox_bot_is_installed()
{
   $dvzBot = dvzBot_init();
   return $dvzBot->is_installed();
}

function dvz_shoutbox_bot_install()
{
   $dvzBot = dvzBot_init();
   $dvzBot->install();
}

function dvz_shoutbox_bot_uninstall()
{
   $dvzBot = dvzBot_init();
   $dvzBot->uninstall();
}

function dvz_shoutbox_bot_admin_user_menu(&$sub_menu)
{
   global $lang;
   $lang->load('dvz_shoutbox_bot');
   $sub_menu[] = ['id' => 'dvz_shoutbox_bot', 'title' => 'DVZ ShoutBox Bot', 'link' => 'index.php?module=user-dvz_shoutbox_bot'];
}

function dvz_shoutbox_bot_user_action_handler(&$actions)
{
   $actions['dvz_shoutbox_bot'] = ['active' => 'dvz_shoutbox_bot', 'file' => 'dvz_shoutbox_bot.php'];
}

function dvz_shoutbox_bot_action_register()
{
   global $db, $mybb;
   if($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_register_on'])
   {
      $row = $db->fetch_array($db->simple_select("users", "username", "", ['order_by' => 'regdate DESC', 'limit' => 1]));
      $bot['message'] = $mybb->settings['dvz_shoutbox_bot_register_message'];
      if($mybb->settings['dvz_shoutbox_bot_link_on'])
      {
         $bot['message'] = str_replace('{username}', "@\"".$row['username']."\"", $bot['message']);
      }else{
         $bot['message'] = str_replace('{username}', $row['username'], $bot['message']);
      }
      $dvzBot = dvzBot_init();
      $dvzBot->shout($bot['message']);
   }
}

function dvz_shoutbox_bot_action_thread()
{
   global $db, $mybb, $new_thread;
   if($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_thread_on'])
   {
      $row = $db->fetch_array($db->simple_select("threads", "tid, fid, username, subject", "", ['order_by' => 'dateline DESC', 'limit' => 1]));

      $forum = get_forum($row['fid']);
      $bot['message'] = $mybb->settings['dvz_shoutbox_bot_thread_message'];
      $link = '[url='.$mybb->settings['bburl'].'/'.get_thread_link($row['tid']).']'.$db->escape_string($row['subject']).'[/url]';
      $link2 = '[url='.$mybb->settings['bburl'].'/'.get_forum_link($row['fid']).']'.$db->escape_string($forum['name']).'[/url]';
      $bot['message'] = str_replace('{subject}', $link, $bot['message']);
      $bot['message'] = str_replace('{forum}', $link2, $bot['message']);
      if($mybb->settings['dvz_shoutbox_bot_link_on'])
      {
         $bot['message'] = str_replace('{username}', "@\"".$row['username']."\"", $bot['message']);
      }else{
         $bot['message'] = str_replace('{username}', $row['username'], $bot['message']);
      }
      if(!$new_thread['savedraft'])
      {
         $ignore = explode(',', $mybb->settings['dvz_shoutbox_bot_ignore']);
         if(!in_array($row['fid'], $ignore))
         {
            $dvzBot = dvzBot_init();
            $dvzBot->shout($bot['message']);
         }
      }
   }
}

function dvz_shoutbox_bot_action_post()
{
   global $db, $mybb;
   if($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_post_on'])
   {
      $row = $db->fetch_array($db->simple_select("posts", "pid, tid, fid, username, subject", "", ['order_by' => 'dateline DESC', 'limit' => 1]));

      $link = '[url='.$mybb->settings['bburl'].'/'.get_post_link($row['pid'], $row['tid']).'#pid'.$row['pid'].']'.$row['subject'].'[/url]';
      $bot['message'] = $mybb->settings['dvz_shoutbox_bot_post_message'];
      $bot['message'] = str_replace('{subject}', $link, $bot['message']);
      if($mybb->settings['dvz_shoutbox_bot_link_on'])
      {
         $bot['message'] = str_replace('{username}', "@\"".$row['username']."\"", $bot['message']);
      }else{
         $bot['message'] = str_replace('{username}', $row['username'], $bot['message']);
      }
      $ignore = explode(',', $mybb->settings['dvz_shoutbox_bot_ignore']);
      if(!in_array($row['fid'], $ignore))
      {
         $dvzBot = dvzBot_init();
         $dvzBot->shout($bot['message']);
      }
   }
}

function dvz_shoutbox_bot_action(&$data)
{
   global $db, $mybb;
   if($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_action_on'])
   {
      $data['text'] = $db->escape_string($data['text']);
      $row = $db->fetch_array($db->simple_select("dvz_shoutbox_bot", "string, answer", "string='{$data['text']}'"));
      if($data['text'] === $row['string'])
      {
         $dvzBot = dvzBot_init();
         $dvzBot->shout($row['answer']);
      }
   }
}
?>
