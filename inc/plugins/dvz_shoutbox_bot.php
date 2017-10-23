<?php
if(!defined("IN_MYBB"))
{
   die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("member_do_register_end", "dvz_shoutbox_bot_action_register");
$plugins->add_hook("datahandler_post_insert_thread_end", "dvz_shoutbox_bot_action_thread");

function dvz_shoutbox_bot_info()
{
   return array(
      "name"            => "DVZ ShoutBox Bot",
      "description"     => "Bot wysyłający wiadomość na czacie, jeżeli użytkownik dokona rejestracji lub napisze nowy wątek.",
      "website"         => "http://sharkservers.eu",
      "author"          => "Qwizi",
      "authorsite"    => "http://sharkservers.eu",
      "version"         => "1.1",
      "compatibility"   => "18*",
      "codename"     => "",
   );
}

function dvz_shoutbox_bot_is_installed()
{
   global $mybb;
   return $mybb->settings['dvz_shoutbox_bot_on'] !== null;
}

function dvz_shoutbox_bot_install()
{
   global $db;
   $group = array(
      "gid"             => "NULL",
      "title"            => "DVZ ShoutBox Bot",
      "name"         => "dvz_shoutbox_bot",
      "description"  => "Ustawienia pluginu DVZ ShoutBox Bot",
      "disporder"     => "1",
      "isdefault"       => "0",
   );
   $db->insert_query("settinggroups", $group);
   $gid = $db->insert_id();

   $setting  = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_on",
      "title"             => "Plugin włączony/wyłączony",
      "description"  => "Plugin ma być włączony/wyłączony?",
      "optionscode"   => "yesno",
      "value"           => "yes",
      "disporder"     => "1",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);

   $setting  = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_link_on",
      "title"             => "Pobierac link do loginu?",
      "description"  => "Określa, czy plugin ma pobierać link do profilu. Przykład @\"Nick\". Wymagany plugin dvz mentions",
      "optionscode"   => "yesno",
      "value"           => "yes",
      "disporder"     => "2",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);

   $setting  = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_id",
      "title"             => "ID bota",
      "description"  => "Podaj id użytkownika, który będzie wysyłał wiadomość na czacie.",
      "optionscode"   => "text",
      "value"           => "1",
      "disporder"     => "3",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);

   $setting  = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_register_on",
      "title"             => "Czy bot ma wysyłać wiadomość na czacie, gdy użytkownik dokona rejestracji?",
      "description"  => "Określa czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik dokona rejestracji.",
      "optionscode"   => "yesno",
      "value"           => "yes",
      "disporder"     => "4",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);

   $setting = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_register_message",
      "title"             => "Wiadomość wysyłana na czacie, jeżeli użytkownik dokona rejestracji",
      "description"  => "Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b> aby zastąpić login",
      "optionscode"   => "textarea",
      "value"           => "Właśnie zarejestrował się {username}. Serdecznie witamy!",
      "disporder"     => "5",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);

   $setting  = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_thread_on",
      "title"             => "Czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy wątek?",
      "description"  => "Określa, czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy wątek.",
      "optionscode"   => "yesno",
      "value"           => "yes",
      "disporder"     => "6",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);

   $setting = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_thread_message",
      "title"             => "Wiadomość wysyłana na czacie, jeżeli użytkownik napisze nowy wątek",
      "description"  => "Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b>, aby zastąpić login. A <b>{subject}</b>, aby pobrać tytuł wątku.",
      "optionscode"   => "textarea",
      "value"           => "Nowy wątek - {subject}. Napisany przez {username}",
      "disporder"     => "7",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);
   rebuild_settings();
}

function dvz_shoutbox_bot_uninstall()
{
   global $db;
   $db->delete_query("settinggroups", "name=\"dvz_shoutbox_bot\"");
   $db->delete_query("settings", "name LIKE \"dvz_shoutbox_bot%\"");
   rebuild_settings();
}

function dvz_shoutbox_bot_action_register()
{
   global $mybb, $db, $data;
   if($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_register_on'])
   {
      $sql = "SELECT username, regdate FROM ".TABLE_PREFIX."users ORDER BY regdate DESC LIMIT 1";
      $result = $db->query($sql);
      $row = $db->fetch_array($result);

      $bot['id'] = $mybb->settings['dvz_shoutbox_bot_id'];
      
      $botregister['username'] = $row['username'];
      $botregister['message'] = $mybb->settings['dvz_shoutbox_bot_register_message'];
      if($mybb->settings['dvz_shoutbox_bot_link_on'])
      {
         $botregister['text'] = str_replace('{username}', "@\"".$botregister['username']."\"", $botregister['message']);
      }else{
         $botregister['text'] = str_replace('{username}', $botregister['username'], $botregister['message']);
      }
      $query = array(
         "id"             => $data['id'],
         "uid"           => $bot['id'],
         "text"         => $botregister['text'],
         "date"      => TIME_NOW,
         "modified"  => "",
         "ipaddress" => "",
      );
      $db->insert_query("dvz_shoutbox", $query);
   }
}

function dvz_shoutbox_bot_action_thread()
{
   global $db, $mybb, $data;
   if($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_thread_on'])
   {
      $sql = "SELECT tid, username, subject, dateline FROM ".TABLE_PREFIX."threads ORDER BY dateline DESC LIMIT 1";
      $result = $db->query($sql);
      $row = $db->fetch_array($result);

      $bot['id'] = $mybb->settings['dvz_shoutbox_bot_id'];

      $botthread['username'] = $row['username'];
      $botthread['subject'] = $row['subject'];
      $botthread['subjectlink'] = get_thread_link($row['tid']);
      $botthread['message'] = $mybb->settings['dvz_shoutbox_bot_thread_message'];
      $link = "[url=".$mybb->settings['bburl']."/".$botthread['subjectlink']."]".$botthread['subject']."[/url]";
      $botthread['message'] = str_replace('{subject}', $link, $botthread['message']);
      if($mybb->settings['dvz_shoutbox_bot_link_on'])
      {
         $botthread['message'] = str_replace('{username}', "@\"".$botthread['username']."\"", $botthread['message']);
      }else{
         $botthread['message'] = str_replace('{username}', $botthread['username'], $botthread['message']);
      }
      $query = array(
         "id"             => $data['id'],
         "uid"           => $bot['id'],
         "text"         => $botthread['message'],
         "date"      => TIME_NOW,
         "modified"  => "",
         "ipaddress" => "",
      );
      $db->insert_query("dvz_shoutbox", $query);
   }
}
?>
