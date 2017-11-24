<?php
if(!defined("IN_MYBB"))
{
   die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("member_do_register_end", "dvz_shoutbox_bot_action_register");
$plugins->add_hook("datahandler_post_insert_thread_end", "dvz_shoutbox_bot_action_thread");
$plugins->add_hook("datahandler_post_insert_post_end", "dvz_shoutbox_bot_action_post");
$plugins->add_hook("dvz_shoutbox_shout_commit", "dvz_shoutbox_bot_action");

function dvz_shoutbox_bot_info()
{
   return array(
      "name"            => "DVZ ShoutBox Bot",
      "description"     => "Bot wysyłający wiadomość na czacie, jeżeli użytkownik dokona rejestracji lub napisze nowy wątek/post. Potrafi też odpowiadać na wiadomości podane przez admina.",
      "website"         => "http://sharkservers.eu",
      "author"          => "Qwizi",
      "authorsite"    => "http://sharkservers.eu",
      "version"         => "1.3.1",
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

   $setting = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_action_on",
      "title"             => "Czy bot ma reagować na wiadomości wpisane na czacie?",
      "description"  => "Określa czy bot ma odpowiadać na wiadomości podane w panelu admina. Można zarządzać nimi <a href=\"index.php?module=user-dvz-shoutbox-bot\">tutaj</a>",
      "optionscode"   => "yesno",
      "value"           => "yes",
      "disporder"     => "3",
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
      "disporder"     => "4",
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
      "disporder"     => "5",
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
      "disporder"     => "6",
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
      "disporder"     => "7",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);

   $setting = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_thread_message",
      "title"             => "Wiadomość wysyłana na czacie, jeżeli użytkownik napisze nowy wątek",
      "description"  => "Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b>, aby zastąpić login, <b>{subject}</b>, aby pobrać tytuł wątku, <b>{forum}</b>, aby pobrać nazwe działu",
      "optionscode"   => "textarea",
      "value"           => "Nowy wątek - {subject} w dziale {forum}. Napisany przez {username}",
      "disporder"     => "8",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);

   $setting  = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_post_on",
      "title"             => "Czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy post?",
      "description"  => "Określa, czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy post.",
      "optionscode"   => "yesno",
      "value"           => "yes",
      "disporder"     => "9",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);

   $setting = array(
      "sid"             => "NULL",
      "name"         => "dvz_shoutbox_bot_post_message",
      "title"             => "Wiadomość wysyłana na czacie, jeżeli użytkownik napisze nowy post",
      "description"  => "Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b>, aby zastąpić login. A <b>{subject}</b>, aby pobrać tytuł postu.",
      "optionscode"   => "textarea",
      "value"           => "Nowy post - {subject}. Napisany przez {username}",
      "disporder"     => "10",
      "gid"              => intval($gid),
   );
   $db->insert_query("settings", $setting);
   rebuild_settings();

   if(!$db->table_exists("dvz_shoutbox_bot"))
   {
      $db->write_query("CREATE TABLE `".TABLE_PREFIX."dvz_shoutbox_bot` ( `id` INT NOT NULL AUTO_INCREMENT , `string` TEXT NOT NULL , `answer` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci".$db->build_create_table_collation().";");
   }
}

function dvz_shoutbox_bot_uninstall()
{
   global $db;
   $db->delete_query("settinggroups", "name=\"dvz_shoutbox_bot\"");
   $db->delete_query("settings", "name LIKE \"dvz_shoutbox_bot%\"");
   if($db->table_exists("dvz_shoutbox_bot"))
   {
      $db->drop_table("dvz_shoutbox_bot");
   }
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

      $bot_register['username'] = $row['username'];
      $bot_register['message'] = $mybb->settings['dvz_shoutbox_bot_register_message'];
      if($mybb->settings['dvz_shoutbox_bot_link_on'])
      {
         $bot_register['text'] = str_replace('{username}', "@\"".$bot_register['username']."\"", $bot_register['message']);
      }else{
         $bot_register['text'] = str_replace('{username}', $bot_register['username'], $bot_register['message']);
      }
      $query = array(
         "id"             => $data['id'],
         "uid"           => $bot['id'],
         "text"         => $bot_register['text'],
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
      $sql = "SELECT tid, fid, username, subject, dateline FROM ".TABLE_PREFIX."threads ORDER BY dateline DESC LIMIT 1";
      $result = $db->query($sql);
      $row = $db->fetch_array($result);

      $bot['id'] = $mybb->settings['dvz_shoutbox_bot_id'];

      $bot_thread['username'] = $row['username'];
      $bot_thread['subject'] = htmlspecialchars($row['subject'], ENT_QUOTES);
      $forum = get_forum($row['fid']);
      $bot_thread['forum'] = $forum['name'];
      $bot_thread['subjectlink'] = get_thread_link($row['tid']);
      $bot_thread['forumlink'] = get_forum_link($row['fid']);
      $bot_thread['message'] = $mybb->settings['dvz_shoutbox_bot_thread_message'];
      $link = "[url=".$mybb->settings['bburl']."/".$bot_thread['subjectlink']."]".$bot_thread['subject']."[/url]";
      $link2 = "[url=".$mybb->settings['bburl']."/".$bot_thread['forumlink']."]".$bot_thread['forum']."[/url]";
      $bot_thread['message'] = str_replace('{subject}', $link, $bot_thread['message']);
      $bot_thread['message'] = str_replace('{forum}', $link2, $bot_thread['message']);
      if($mybb->settings['dvz_shoutbox_bot_link_on'])
      {
         $bot_thread['message'] = str_replace('{username}', "@\"".$bot_thread['username']."\"", $bot_thread['message']);
      }else{
         $bot_thread['message'] = str_replace('{username}', $bot_thread['username'], $bot_thread['message']);
      }
      $query = array(
         "id"             => $data['id'],
         "uid"           => $bot['id'],
         "text"         => $bot_thread['message'],
         "date"      => TIME_NOW,
         "modified"  => "",
         "ipaddress" => "",
         );
      $db->insert_query("dvz_shoutbox", $query);
   }
}

function dvz_shoutbox_bot_action_post()
{
   global $db, $mybb, $data;
   if($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_post_on'])
   {
      $sql = "SELECT pid, tid, username, subject, dateline FROM ".TABLE_PREFIX."posts ORDER BY dateline DESC LIMIT 1";
      $result = $db->query($sql);
      $row = $db->fetch_array($result);

      $bot['id'] = $mybb->settings['dvz_shoutbox_bot_id'];

      $bot_post['username'] = $row['username'];
      $bot_post['subject']  = htmlspecialchars($row['subject'], ENT_QUOTES);
      $bot_post['subjectlink'] = get_post_link($row['pid'], $row['tid']);
      $link = "[url=".$mybb->settings['bburl']."/".$bot_post['subjectlink']."#pid".$row['pid']."]".$bot_post['subject']."[/url]";
      $bot_post['message'] = $mybb->settings['dvz_shoutbox_bot_post_message'];
      $bot_post['message'] = str_replace('{subject}', $link, $bot_post['message']);
      if($mybb->settings['dvz_shoutbox_bot_link_on'])
      {
         $bot_post['message'] = str_replace('{username}', "@\"".$bot_post['username']."\"", $bot_post['message']);
      }else{
         $bot_post['message'] = str_replace('{username}', $bot_post['username'], $bot_post['message']);
      }
      $query = array(
         "id"             => $data['id'],
         "uid"           => $bot['id'],
         "text"         => $bot_post['message'],
         "date"      => TIME_NOW,
         "modified"  => "",
         "ipaddress" => "",
      );
      $db->insert_query("dvz_shoutbox", $query);
   }
}

function dvz_shoutbox_bot_action($data)
{
   global $db, $mybb;
   if($mybb->settings['dvz_shoutbox_bot_on'] && $mybb->settings['dvz_shoutbox_bot_action_on'])
   {
      $query = $db->simple_select("dvz_shoutbox_bot", "*", "string=\"{$data['text']}\"");
      $row = $db->fetch_array($query);

      $bot_action['string'] = $row['string'];
      $bot_action['answer'] = $row['answer'];

      $bot['id'] = $mybb->settings['dvz_shoutbox_bot_id'];

      if($data['text'] == $bot_action['string'])
      {
         $query = array(
            "id"             => $data['id'],
            "uid"           => $bot['id'],
            "text"         => $bot_action['answer'],
            "date"      => TIME_NOW,
            "modified"  => "",
            "ipaddress" => "",
         );
         $db->insert_query("dvz_shoutbox", $query);
      }
   }
}
?>
