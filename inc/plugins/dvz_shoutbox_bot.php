<?php
declare (strict_types = 1);

use Qwizi\Core\ClassLoader;
use Qwizi\DVZSB\Bot;

defined('IN_MYBB') or die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');

defined('PLUGINLIBRARY') or define('PLUGINLIBRARY', MYBB_ROOT . "inc/plugins/pluginlibrary.php");

defined('QWIZI_PLUGINS_CORE_PATH') || define('QWIZI_PLUGINS_CORE_PATH', __DIR__ . '/QwiziPlugins/Core');

define('DVZSB_PLUGIN_PATH', __DIR__ . '/QwiziPlugins/DVZSB');

require_once QWIZI_PLUGINS_CORE_PATH . '/src/ClassLoader.php';
$classLoader = ClassLoader::getInstance();
$classLoader->registerNamespace(
    'Qwizi\\DVZSB\\',
    DVZSB_PLUGIN_PATH . '/src/'
);
$classLoader->register();

// TODO Usunąć hooka index_end
$plugins->add_hook('index_end', 'dvz_shoutbox_bot_index');
$plugins->add_hook('member_do_register_end', 'dvz_shoutbox_bot_register');
$plugins->add_hook('datahandler_post_insert_thread_end', 'dvz_shoutbox_bot_thread');
$plugins->add_hook('datahandler_post_insert_post_end', 'dvz_shoutbox_bot_post');
$plugins->add_hook('dvz_shoutbox_shout_commit', 'dvz_shoutbox_bot_shout_commit');
$plugins->add_hook('admin_user_menu', 'dvz_shoutbox_bot_admin_user_menu');
$plugins->add_hook('admin_user_action_handler', 'dvz_shoutbox_bot_user_action_handler');

function dvz_shoutbox_bot_info()
{
    global $lang, $db;
    $lang->load('dvz_shoutbox_bot');

    return [
        'name' => $db->escape_string($lang->bot_title),
        'description' => $db->escape_string($lang->bot_desc),
        'author' => 'Adrian \'Qwizi\' Ciołek, Poftorek',
        'authorsite' => 'https://github.com/Qwizi',
        'version' => '1.5.0',
        'compatibility' => '18*',
        'codename' => '',
    ];
}

function dvz_shoutbox_bot_install()
{
    global $db, $PL, $lang;

    if (!file_exists(PLUGINLIBRARY)) {
        flash_message("PluginLibrary is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }

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
            'commands_onoff' => [ // TODO przetłumaczyć ustawienie komendy onoff
                'title' => $lang->bot_commands_onoff_title,
                'description' => $lang->bot_commands_onoff_desc,
                'optionscode' => 'onoff',
                'value' => 1,
            ],
            'commands_prefix' => [ // TODO przetłaczyć ustawienie prefix
                'title' => $lang->bot_commands_prefix_title,
                'description' => $lang->bot_commands_prefix_desc,
                'optionscode' => 'text',
                'value' => '/',
            ],
        ]
    );

    $query = $db->query("SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB'");
    $innodbSupport = $db->num_rows($query) && in_array($db->fetch_field($query, 'SUPPORT'), ['DEFAULT', 'YES']);

    $db->write_query("
        CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "dvz_shoutbox_bot_commands` (
            `cid` int(11) not null auto_increment,
            `tag` varchar(24) not null,
            `name` varchar(64) not null,
            `command` varchar(32) not null,
            `description` text not null,
            `activated` tinyint(1) not null default 1,
            PRIMARY KEY (`cid`)
        ) " . ($innodbSupport ? "ENGINE=InnoDB" : null) . " " . $db->build_create_table_collation() . "
    ");

    $commandsData = [
        [
            'tag' => 'ban',
            'name' => $lang->bot_commandsData_ban_name,
            'command' => 'ban',
            'description' => $lang->bot_commandsData_ban_desc,
            'activated' => 1,
        ],
        [
            'tag' => 'unBan',
            'name' => $lang->bot_commandsData_unBan_name,
            'command' => 'unban',
            'description' => $lang->bot_commandsData_unBan_desc,
            'activated' => 1,
        ],
        [
            'tag' => 'banList',
            'name' => $lang->bot_commandsData_banList_name,
            'command' => 'banlist',
            'description' => $lang->bot_commandsData_banList_desc,
            'activated' => 1,
        ],
        [
            'tag' => 'prune',
            'name' => $lang->bot_commandsData_prune_name,
            'command' => 'prune',
            'description' => $lang->bot_commandsData_prune_desc,
            'activated' => 1,
        ],
        [
            'tag' => 'setBot',
            'name' => $lang->bot_commandsData_setBot_name,
            'command' => 'setbot',
            'description' => $lang->bot_commandsData_setBot_desc,
            'activated' => 1,
        ],
        [
            'tag' => 'help',
            'name' => $lang->bot_commandsData_help_name,
            'command' => 'help',
            'description' => $lang->bot_commandsData_help_desc,
            'activated' => 1,
        ],
        [
            'tag' => 'steamID64',
            'name' => 'SteamID32 -> SteamID64',
            'command' => 'steamid64',
            'description' => $lang->bot_commandsData_steamID64_desc,
            'activated' => 1,
        ],
        [
            'tag' => 'steamID32',
            'name' => 'SteamID64 -> SteamID32',
            'command' => 'steamid32',
            'description' => $lang->bot_commandsData_steamID32_desc,
            'activated' => 1,
        ],
    ];

    //! ADD COMMANDS
    $db->insert_query_multiple('dvz_shoutbox_bot_commands', $commandsData);

    //! UPDATE CACHE
    $PL->cache_update('dvz_shoutbox_bot', [
        'version' => dvz_shoutbox_bot_info()['version'],
        'commands' => $commandsData,
    ]);
}

function dvz_shoutbox_bot_uninstall()
{
    global $db, $PL;

    if (!file_exists(PLUGINLIBRARY)) {
        flash_message("PluginLibrary is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }

    $PL or require_once PLUGINLIBRARY;

    $PL->settings_delete('dvz_sb_bot', true);
    $PL->cache_delete('dvz_shoutbox_bot');

    //! Delete old settings
    $query = $db->simple_select('settinggroups', 'gid', "name='dvz_shoutbox_bot'");
    if ((bool) $db->num_rows($query)) {
        $db->delete_query('settinggroups', 'name=\'dvz_shoutbox_bot\'');
        $db->delete_query('settings', 'name LIKE \'dvz_shoutbox_bot%\'');
    }

    //! Delete old table
    if ($db->table_exists('dvz_shoutbox_bot')) {
        $db->drop_table('dvz_shoutbox_bot');
    }

    // Delete commands table
    if ($db->table_exists('dvz_shoutbox_bot_commands')) {
        $db->drop_table('dvz_shoutbox_bot_commands');
    }
}

function dvz_shoutbox_bot_is_installed()
{
    global $db;
    $query = $db->simple_select('settinggroups', 'gid', "name='dvz_sb_bot'");
    return (bool) $db->num_rows($query);
}

function dvz_shoutbox_bot_activate()
{
    global $PL;
    $PL or require_once PLUGINLIBRARY;

    $pluginCache = $PL->cache_read('dvz_shoutbox_bot');

    if (isset($pluginCache['version']) && version_compare($pluginCache['version'], dvz_shoutbox_bot_info()['version']) == -1) {
        $pluginCache['version'] = dvz_shoutbox_bot_info()['version'];
        $PL->update_cache('dvz_shoutbox_bot', ['version' => $pluginCache]);
    }
}

function dvz_shoutbox_bot_admin_user_menu(&$sub_menu)
{
    $sub_menu[count($sub_menu) - 1] = [
        'id' => 'dvz-shoutbox-bot',
        'title' => 'DVZ ShoutBox Bot',
        'link' => 'index.php?module=user-dvz-shoutbox-bot',
    ];
}

function dvz_shoutbox_bot_user_action_handler(&$actions)
{
    $actions['dvz-shoutbox-bot'] = ['active' => 'dvz-shoutbox-bot', 'file' => 'dvz_shoutbox_bot.php'];
}

function dvz_shoutbox_bot_register()
{
    global $user;
    dvz_shoutbox_bot_create_instance();

    if (Bot::getInstance()->settings('register_onoff')) {
        $message = Bot::getInstance()->createMsg('register', [
            'username' => $user['username'],
        ]);
        Bot::getInstance()->shout($message);
    }
}

function dvz_shoutbox_bot_thread(&$data)
{
    dvz_shoutbox_bot_create_instance();

    if (Bot::getInstance()->settings('thread_onoff')) {
        $data = (array) $data;

        if ($data['return_values']['visible'] != -2) {

            $thread = get_thread($data['return_values']['tid']);
            $forum = get_forum($thread['fid']);
            $forumIgnore = explode(",", Bot::getInstance()->settings('forum_ignore'));

            if (!in_array($forum['fid'], $forumIgnore) && !in_array("-1", $forumIgnore)) {

                $threadLink = get_thread_link($thread['tid']);
                $threadTitle = htmlspecialchars_uni($thread['subject']);
                $threadFullLink = Bot::getInstance()->createLink($threadLink, $threadTitle);

                $forumLink = get_forum_link($forum['fid']);
                $forumTitle = htmlspecialchars_uni($forum['name']);
                $forumFullLink = Bot::getInstance()->createLink($forumLink, $forumTitle);

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

                $message = Bot::getInstance()->createMsg('thread', [
                    'username' => $thread['username'],
                    'subject' => $threadFullLink,
                    'forum' => $forumFullLink,
                    'message' => $post['message'],
                    'pid' => $post['pid'],
                    'dateline' => $thread['dateline'],
                ]);

                Bot::getInstance()->shout($message);
            }

        }
    }
}

function dvz_shoutbox_bot_post(&$data)
{
    dvz_shoutbox_bot_create_instance();

    if (Bot::getInstance()->settings('post_onoff')) {
        $data = (array) $data;

        $post = get_post($data['return_values']['pid']);

        $forumIgnore = explode(",", Bot::getInstance()->settings('forum_ignore'));

        if (!in_array($post['fid'], $forumIgnore) && !in_array("-1", $forumIgnore)) {
            $postLink = get_post_link($post['pid'], $post['tid']);
            $postLinkPid = $postLink . '#pid' . $post['pid'];
            $postTitle = htmlspecialchars_uni($post['subject']);
            $postFullLink = Bot::getInstance()->createLink($postLinkPid, $postTitle);

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

            $message = Bot::getInstance()->createMsg('post', [
                'username' => $post['username'],
                'subject' => $postFullLink,
                'message' => $post['message'],
                'pid' => $post['pid'],
                'dateline' => $post['dateline'],
            ]);

            Bot::getInstance()->shout($message);
        }
    }
}

function dvz_shoutbox_bot_shout_commit(&$data)
{
    dvz_shoutbox_bot_create_instance();

    if (Bot::getInstance()->settings('commands_onoff')) {
        $PL = Bot::getInstance()->getPL();
        $pluginCache = $PL->cache_read('dvz_shoutbox_bot');

        $commandsArray = $pluginCache['commands'];

        if (!empty($commandsArray)) {
            foreach ($commandsArray as &$command) {

                if ($command['activated'] == 1) {

                    $data['command'] = $command['command'];

                    $nameSpace = 'Qwizi\\DVZSB\\Commands\\';
                    $commandClassName = $nameSpace . ucfirst($command['tag']);

                    $commandClass = new $commandClassName(Bot::getInstance());
                    $commandClass->doAction($data);
                }
            }
        }
    }
}

function dvz_shoutbox_bot_index()
{
    dvz_shoutbox_bot_create_instance();
    $PL = Bot::getInstance()->getPL();

    $pluginCache = $PL->cache_read('dvz_shoutbox_bot');

    $commandsArray = $pluginCache['commands'];
    $lang = Bot::getInstance()->getLang();
    print_r($lang->load('dvz_shoutbox_bot'));
/*     $key = array_search('test', array_column($commandsArray, 'tag'));
unset($commandsArray[$key]);
print_r($key);
print_r($commandsArray);*/
}

function dvz_shoutbox_bot_create_instance()
{
    global $mybb, $db, $lang, $PL, $bot;
    $PL or require_once PLUGINLIBRARY;

    $bot = Bot::createInstance($mybb, $db, $lang, $PL);
}
