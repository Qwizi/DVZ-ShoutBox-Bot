<?php
ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
if (!defined('IN_MYBB')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

if (!defined("PLUGINLIBRARY")) {
    define("PLUGINLIBRARY", MYBB_ROOT . "inc/plugins/pluginlibrary.php");
}

if (!defined("QWIZI_CORE_PATH")) {
    define("QWIZI_CORE_PATH", MYBB_ROOT . 'inc/plugins/qwizi/core/');
}

if (!defined("QWIZI_BOT_PLUGIN_PATH")) {
    define("QWIZI_BOT_PLUGIN_PATH", MYBB_ROOT . 'inc/plugins/qwizi/dvz_shoutbox_bot');
}

require_once QWIZI_CORE_PATH . 'ClassLoader.php';
/* Ladowanie klas*/
$classLoader = new MybbStuff_Core_ClassLoader();
$classLoader->registerNamespace(
    'Qwizi_DVZSB',
    [QWIZI_BOT_PLUGIN_PATH . '/']
);
$classLoader->register();

$plugins->add_hook('index_end', 'dvz_shoutbox_bot_index');
$plugins->add_hook('member_do_register_end', 'dvz_shoutbox_bot_register');
$plugins->add_hook('datahandler_post_insert_thread_end', 'dvz_shoutbox_bot_thread');
$plugins->add_hook('datahandler_post_insert_post_end', 'dvz_shoutbox_bot_post');
$plugins->add_hook('dvz_shoutbox_shout_commit', 'dvz_shoutbox_bot_shout_commit');

function dvz_shoutbox_bot_info()
{
    global $lang, $db;
    $lang->load('dvz_shoutbox_bot');

    return [
        'name' => $db->escape_string($lang->bot_title),
        'description' => $db->escape_string($lang->bot_desc),
        'author' => 'Adrian \'Qwizi\' Ciołek',
        'authorsite' => 'https://github.com/Qwizi',
        'version' => '1.5',
        'compatibility' => '18*',
        'codename' => '',
    ];
}

function dvz_shoutbox_bot_install()
{
    global $db, $PL, $lang;
    $PL or require_once PLUGINLIBRARY;

    $lang->load('dvz_shoutbox_bot');

    $PL->settings(
        'dvz_sb_bot',
        $lang->bot_title,
        $lang->bot_setting_desc,
        [
            'id' => [
                'title' => $lang->bot_id_title,
                'description' => $lang->bot_id_desc,
                'optionscode' => 'numeric',
                'value' => 1,
            ],
            'register_onoff' => [
                'title' => $lang->bot_register_title,
                'description' => $lang->bot_register_desc,
                'optionscode' => 'onoff',
                'value' => 1,
            ],
            'register_message' => [
                'title' => $lang->bot_register_message_title,
                'description' => $lang->bot_register_message_desc,
                'optionscode' => 'textarea',
                'value' => $lang->bot_register_message_example,
            ],
            'thread_onoff' => [
                'title' => $lang->bot_thread_title,
                'description' => $lang->bot_thread_desc,
                'optionscode' => 'onoff',
                'value' => 1,
            ],
            'forum_ignore' => [
                'title' => $lang->bot_ignore_title,
                'description' => $lang->bot_ignore_desc,
                'optionscode' => 'forumselect',
                'value' => '',
            ],
            'thread_message' => [
                'title' => $lang->bot_thread_message_title,
                'description' => $lang->bot_thread_message_desc,
                'optionscode' => 'textarea',
                'value' => $lang->bot_thread_message_example,
            ],
            'thread_message' => [
                'title' => $lang->bot_thread_message_title,
                'description' => $lang->bot_thread_message_desc,
                'optionscode' => 'textarea',
                'value' => $lang->bot_thread_message_example,
            ],
            'post_onoff' => [
                'title' => $lang->bot_post_title,
                'description' => $lang->bot_post_desc,
                'optionscode' => 'onoff',
                'value' => 1,
            ],
            'post_message' => [
                'title' => $lang->bot_post_message_title,
                'description' => $lang->bot_post_message_desc,
                'optionscode' => 'textarea',
                'value' => $lang->bot_post_message_example,
            ],
            'commands_ononff' => [
                'title' => 'Komendy włączone/wyłączone',
                'description' => 'Określa czy komenda mają być włączone',
                'optionscode' => 'onoff',
                'value' => 1,
            ],
            'commands_prefix' => [
                'title' => 'Prefix do komend',
                'description' => 'Określa prefix do komend',
                'optionscode' => 'text',
                'value' => '/',
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
    $PL or require_once PLUGINLIBRARY;

    $PL->settings_delete('dvz_sb_bot', true);

    //Delete settings, if plugin version < 1.5
    $query = $db->simple_select('settinggroups', 'gid', "name='dvz_shoutbox_bot'");
    if ((bool) $db->num_rows($query)) {
        $db->delete_query('settinggroups', 'name=\'dvz_shoutbox_bot\'');
        $db->delete_query('settings', 'name LIKE \'dvz_shoutbox_bot%\'');
    }

    if ($db->table_exists('dvz_shoutbox_bot')) {
        $db->drop_table('dvz_shoutbox_bot');
    }
}

function dvz_shoutbox_bot_is_installed()
{
    global $db;
    $query = $db->simple_select('settinggroups', 'gid', "name='dvz_sb_bot'");
    return (bool) $db->num_rows($query);
}

function dvz_shoutbox_bot_register()
{
    global $user;
    dvz_shoutbox_bot_create_instance();

    if (Qwizi_DVZSB_Bot::getInstance()->settings('register_onoff')) {
        $message = Qwizi_DVZSB_Bot::getInstance()->createMsg('register', [
            'username' => $user['username'],
        ]);
        Qwizi_DVZSB_Bot::getInstance()->shout($message);
    }
}

function dvz_shoutbox_bot_thread(&$data)
{
    dvz_shoutbox_bot_create_instance();

    if (Qwizi_DVZSB_Bot::getInstance()->settings('thread_onoff')) {
        $data = (array) $data;

        if ($data['return_values']['visible'] != -2) {

            $thread = get_thread($data['return_values']['tid']);
            $forum = get_forum($thread['fid']);
            $forumIgnore = explode(",", Qwizi_DVZSB_Bot::getInstance()->settings('forum_ignore'));

            if (!in_array($forum['fid'], $forumIgnore) && !in_array("-1", $forumIgnore)) {

                $threadLink = get_thread_link($thread['tid']);
                $threadTitle = htmlspecialchars_uni($thread['subject']);
                $threadFullLink = Qwizi_DVZSB_Bot::getInstance()->createLink($threadLink, $threadTitle);

                $forumLink = get_forum_link($forum['fid']);
                $forumTitle = htmlspecialchars_uni($forum['name']);
                $forumFullLink = Qwizi_DVZSB_Bot::getInstance()->createLink($forumLink, $forumTitle);

                $post = get_post($data['return_values']['pid']);

                require_once MYBB_ROOT . "inc/class_parser.php";
                $parser = new postParser;

                $parser_options = [
                    'allow_html' => 'no',
                    'allow_mycode' => 'no',
                ];

                $post['message'] = strip_tags($parser->parse_message($post['message'], $parser_options));

                if (my_strlen($post['message']) > 800) {
                    $post['message'] = my_substr($post['message'], 0, 800) . '...';
                }

                $message = Qwizi_DVZSB_Bot::getInstance()->createMsg('thread', [
                    'username' => $thread['username'],
                    'subject' => $threadFullLink,
                    'forum' => $forumFullLink,
                    'message' => $post['message'],
                    'pid' => $post['pid'],
                    'dateline' => $thread['dateline'],
                ]);

                Qwizi_DVZSB_Bot::getInstance()->shout($message);
            }

        }
    }
}

function dvz_shoutbox_bot_post(&$data)
{
    dvz_shoutbox_bot_create_instance();

    if (Qwizi_DVZSB_Bot::getInstance()->settings('post_onoff')) {
        $data = (array) $data;

        $post = get_post($data['return_values']['pid']);

        $forumIgnore = explode(",", Qwizi_DVZSB_Bot::getInstance()->settings('forum_ignore'));

        if (!in_array($post['fid'], $forumIgnore) && !in_array("-1", $forumIgnore)) {
            $postLink = get_post_link($post['pid'], $post['tid']);
            $postLinkPid = $postLink . '#pid' . $post['pid'];
            $postTitle = htmlspecialchars_uni($post['subject']);
            $postFullLink = Qwizi_DVZSB_Bot::getInstance()->createLink($postLinkPid, $postTitle);

            require_once MYBB_ROOT . "inc/class_parser.php";
            $parser = new postParser;

            $parser_options = [
                'allow_html' => 'no',
                'allow_mycode' => 'no',
            ];

            $post['message'] = strip_tags($parser->parse_message($post['message'], $parser_options));

            if (my_strlen($post['message']) > 800) {
                $post['message'] = my_substr($post['message'], 0, 800) . '...';
            }

            $message = Qwizi_DVZSB_Bot::getInstance()->createMsg('post', [
                'username' => $post['username'],
                'subject' => $postFullLink,
                'message' => $post['message'],
                'pid' => $post['pid'],
                'dateline' => $post['dateline'],
            ]);

            Qwizi_DVZSB_Bot::getInstance()->shout($message);
        }
    }
}

function dvz_shoutbox_bot_shout_commit(&$data)
{
    global $mybb;
    dvz_shoutbox_bot_create_instance();

    $commands = ['prune', 'ban', 'unBan', 'banList'];
    for ($i = 0; $i < count($commands); $i++) {
        $class = 'Qwizi_DVZSB_Commands_';
        $commandClass = $class . ucfirst($commands[$i]);
        $command = new $commandClass(Qwizi_DVZSB_Bot::getInstance());

        $command->doAction($data);
        rebuild_settings();
    }
}

function dvz_shoutbox_bot_index()
{
    global $mybb;
    dvz_shoutbox_bot_create_instance();
    $commands = ['Prune', 'UnBan'];
    for ($i = 0; $i < count($commands); $i++) {
        $class = 'Qwizi_DVZSB_Commands_';
        $commandClass = $class . $commands[$i];
        $array = explode(",", $mybb->settings['dvz_sb_groups_mod']);
        $command = new $commandClass(Qwizi_DVZSB_Bot::getInstance());
    }

}

function dvz_shoutbox_bot_create_instance()
{
    global $mybb, $db, $cache;

    Qwizi_DVZSB_Bot::createInstance($mybb, $db, $cache);
}
