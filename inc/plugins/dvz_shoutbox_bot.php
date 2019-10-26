<?php

declare(strict_types=1);

defined('IN_MYBB') or die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');

defined('PLUGINLIBRARY') or define('PLUGINLIBRARY', MYBB_ROOT . "inc/plugins/pluginlibrary.php");

defined('QWIZI_PLUGINS_CORE_PATH') || define('QWIZI_PLUGINS_CORE_PATH', __DIR__ . '/QwiziPlugins/Core');

define('DVZSB_PLUGIN_PATH', __DIR__ . '/QwiziPlugins/DVZSB');

require_once QWIZI_PLUGINS_CORE_PATH . '/src/ClassLoader.php';

$classLoader = \Qwizi\Core\ClassLoader::getInstance();
$classLoader->registerNamespace(
    'Qwizi\\DVZSB\\',
    DVZSB_PLUGIN_PATH . '/src/'
)->register();

$plugins->add_hook('member_do_register_end', 'dvz_shoutbox_bot_register');
$plugins->add_hook('datahandler_post_insert_thread_end', 'dvz_shoutbox_bot_thread');
$plugins->add_hook('datahandler_post_insert_post_end', 'dvz_shoutbox_bot_post');
$plugins->add_hook('dvz_shoutbox_shout_commit', 'dvz_shoutbox_bot_shout_commit');
$plugins->add_hook('dvz_shoutbox_bot_shout', 'dvz_shoutbox_bot_shout_commit');
$plugins->add_hook('admin_user_menu', 'dvz_shoutbox_bot_admin_user_menu');
$plugins->add_hook('admin_user_action_handler', 'dvz_shoutbox_bot_user_action_handler');
$plugins->add_hook('admin_dvz_shoutbox_bot_reload', 'dvz_shoutbox_bot_reload_commands');
$plugins->add_hook('admin_user_permissions', 'dvz_shoutbox_bot_admin_user_permissions');

function dvz_shoutbox_bot_info()
{
    return [
        'name' => 'DVZ ShoutBox Bot',
        'description' => 'Bot sending messages on chat if user will register or write new thread/post and respond to commands',
        'author' => 'Adrian \'Qwizi\' Ciołek',
        'authorsite' => 'https://github.com/Qwizi',
        'version' => '2.0.0',
        'compatibility' => '18*',
        'codename' => 'dvz_shoutbox_bot',
    ];
}

function dvz_shoutbox_bot_install()
{
    global $db, $PL, $lang, $cache;

    if (!file_exists(PLUGINLIBRARY)) {
        flash_message("PluginLibrary is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }

    $PL or require_once PLUGINLIBRARY;

    $lang->load('dvz_shoutbox_bot');

    $PL->settings(
        'dvz_sb_bot',
        'DVZ ShoutBox Bot',
        'Settings of DVZ ShoutBox Bot',
        [
            'id' => [
                'title' => $lang->id_t,
                'description' => $lang->id_d,
                'optionscode' => 'numeric',
                'value' => 1,
            ],
            'forum_ignore' => [
                'title' => $lang->ignore_t,
                'description' => $lang->ignore_d,
                'optionscode' => 'forumselect',
                'value' => '',
            ],
            'register_onoff' => [
                'title' => $lang->register_t,
                'description' => $lang->register_d,
                'optionscode' => 'onoff',
                'value' => 1,
            ],
            'register_message' => [
                'title' => $lang->register_message_t,
                'description' => $lang->register_message_d,
                'optionscode' => 'textarea',
                'value' => $lang->register_message_example,
            ],
            'thread_onoff' => [
                'title' => $lang->thread_t,
                'description' => $lang->thread_d,
                'optionscode' => 'onoff',
                'value' => 1,
            ],
            'thread_message' => [
                'title' => $lang->thread_message_t,
                'description' => $lang->thread_message_d,
                'optionscode' => 'textarea',
                'value' => $lang->thread_message_example,
            ],
            'post_onoff' => [
                'title' => $lang->post_t,
                'description' => $post_d,
                'optionscode' => 'onoff',
                'value' => 1,
            ],
            'post_message' => [
                'title' => $lang->post_message_t,
                'description' => $lang->post_message_d,
                'optionscode' => 'textarea',
                'value' => $lang->post_message_example,
            ],
            'commands_onoff' => [
                'title' => $lang->commands_t,
                'description' => $lang->commands_d,
                'optionscode' => 'onoff',
                'value' => 1,
            ],
            'commands_prefix' => [
                'title' => $lang->commands_prefix_t,
                'description' => $lang->commands_prefix_d,
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

    $db->write_query("
        CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . "dvz_shoutbox_bot_commands_logs` (
            `clid` int(11) not null auto_increment,
            `ctag` varchar(24) not null,
            `message` text not null,
            `date` int(11) not null,
            PRIMARY KEY (`clid`)
        ) " . ($innodbSupport ? "ENGINE=InnoDB" : null) . " " . $db->build_create_table_collation() . "
    ");

    dvz_shoutbox_bot_create_command_manager_instance();

    \Qwizi\DVZSB\CommandManager::i()->createCommand([
        [
            'tag' => 'ban',
            'name' => 'Ban',
            'command' => 'ban',
            'description' => 'This command allows you to ban users',
            'activated' => 1
        ],
        [
            'tag' => 'unBan',
            'name' => 'UnBan',
            'command' => 'unban',
            'description' => 'This command allows you to remove user ban',
            'activated' => 1
        ],
        [
            'tag' => 'banList',
            'name' => 'Ban List',
            'command' => 'banlist',
            'description' => 'This command currently shows who is banned',
            'activated' => 1
        ],
        [
            'tag' => 'prune',
            'name' => 'Prune',
            'command' => 'prune',
            'description' => 'This command allows you to delete entries',
            'activated' => 1
        ],
        [
            'tag' => 'help',
            'name' => 'Help',
            'command' => 'help',
            'description' => 'Commands List',
            'activated' => 1
        ],
        [
            'tag' => 'setBot',
            'name' => 'Set Bot',
            'command' => 'setbot',
            'description' => 'This command allows you to set up a bot account',
            'activated' => 1
        ],
        [
            'tag' => 'myShouts',
            'name' => 'MyShouts',
            'command' => 'myshouts',
            'description' => 'The command displays how many entries you have written on shoutbox',
            'activated' => 1
        ],
        [
            'tag' => 'topShouters',
            'name' => 'TopShouters',
            'command' => 'top10',
            'description' => 'Top 10',
            'activated' => 1
        ]
    ]);

    $new_task = [
        'title'            => 'DVZ ShoutBox Bot',
        'description'    => 'Task delete shouts when text = null.',
        'file'            => 'dvz_shoutbox_bot',
        'minute'        => '*',
        'hour'            => '6',
        'day'            => '*',
        'month'            => '*',
        'weekday'        => '*',
        'enabled'        => '1',
        'logging'        => '1',
    ];

    $new_task['nextrun'] = 0;

    $db->insert_query('tasks', $new_task);
    $cache->update_tasks();
}

function dvz_shoutbox_bot_uninstall()
{
    global $db, $PL, $cache;

    if (!file_exists(PLUGINLIBRARY)) {
        flash_message("PluginLibrary is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }

    $PL or require_once PLUGINLIBRARY;

    $PL->settings_delete('dvz_sb_bot', true);

    $cache->delete('dvz_shoutbox_bot');
    $db->delete_query('tasks', 'file=\'dvz_shoutbox_bot\'');

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

    // Delete commands logs table
    if ($db->table_exists('dvz_shoutbox_bot_commands_logs')) {
        $db->drop_table('dvz_shoutbox_bot_commands_logs');
    }
}

function dvz_shoutbox_bot_is_installed()
{
    global $db;
    $query = $db->simple_select('settinggroups', 'gid', "name='dvz_sb_bot'");
    return (bool) $db->num_rows($query);
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

function dvz_shoutbox_bot_admin_user_permissions(&$admin_permissons)
{
    global $lang;
    $lang->load('dvz_shoutbox_bot');

    $admin_permissons['dvz_shoutbox_bot'] = $lang->can_manage_permission;
}

function dvz_shoutbox_bot_reload_commands()
{
    global $db, $PL;
    $PL or require_once PLUGINLIBRARY;

    dvz_shoutbox_bot_create_instances();
    $commandsDataCache = Command::i()->getCommands();
    $commandsDataJson = getCommandsDataJson();

    $new = array_diff_key($commandsDataJson, $commandsDataCache);
    if (!empty($new)) {
        foreach ($new as $key => $value) {
            $commandsDataDb[] = $value;
        }

        if (count($commandsDataDb) >  0) {
            $db->insert_query_multiple("dvz_shoutbox_bot_commands", $commandsDataDb);
            ;
        } else {
            $db->insert_query("dvz_shoutbox_bot_commands", $commandsDataDb);
        }
    }
    Command::i()->updateCache();
    admin_redirect("index.php?module=user-dvz-shoutbox-bot");
}

function dvz_shoutbox_bot_register()
{
    global $mybb, $user;

    dvz_shoutbox_bot_create_bot_instance();

    if ($mybb->settings['dvz_sb_bot_register_onoff']) {
        $registerMessage = new \Qwizi\DVZSB\Messages\RegisterMessage($mybb->settings['dvz_sb_bot_register_message']);
        $message = $registerMessage->convert(['username' => $user['username']]);
        \Qwizi\DVZSB\Bot::i()->shout($message);
    }
}

function dvz_shoutbox_bot_thread(&$data)
{
    global $mybb;

    dvz_shoutbox_bot_create_bot_instance();

    if ($mybb->settings['dvz_sb_bot_thread_onoff']) {
        $data = (array) $data;

        if ($data['return_values']['visible'] != -2) {
            $thread = get_thread($data['return_values']['tid']);
            $forum = get_forum($thread['fid']);
            $forumIgnore = explode(",", $mybb->settings['dvz_sb_bot_forum_ignore']);

            if (!in_array($forum['fid'], $forumIgnore) && !in_array("-1", $forumIgnore)) {
                $threadMessage = new \Qwizi\DVZSB\Messages\ThreadMessage($mybb->settings['dvz_sb_bot_thread_message']);

                $threadLink = get_thread_link($thread['tid']);
                $threadTitle = htmlspecialchars_uni($thread['subject']);
                $threadFullLink = $threadMessage->createLink(
                    $mybb->settings['bburl'],
                    $threadLink,
                    $threadTitle
                );

                $forumLink = get_forum_link($forum['fid']);
                $forumTitle = htmlspecialchars_uni($forum['name']);
                $forumFullLink = $threadMessage->createLink(
                    $mybb->settings['bburl'],
                    $forumLink,
                    $forumTitle
                );

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

                $message = $threadMessage->convert([
                    'username' => $thread['username'],
                    'subject' => $threadFullLink,
                    'forum' => $forumFullLink,
                    'message' => $post['message'],
                    'pid' => $post['pid'],
                    'dateline' => $thread['dateline'],
                ]);

                \Qwizi\DVZSB\Bot::i()->shout($message);

                /* \Qwizi\DVZSB\Bot::i()->convert('thread', [
                    'username' => $thread['username'],
                    'subject' => $threadFullLink,
                    'forum' => $forumFullLink,
                    'message' => $post['message'],
                    'pid' => $post['pid'],
                    'dateline' => $thread['dateline'],
                ])->shout(\Qwizi\DVZSB\Bot::i()->getMessage()); */
            }
        }
    }
}

function dvz_shoutbox_bot_post(&$data)
{
    global $mybb;

    dvz_shoutbox_bot_create_bot_instance();

    if ($mybb->settings['dvz_sb_bot_post_onoff']) {
        $data = (array) $data;

        $post = get_post($data['return_values']['pid']);

        $forumIgnore = explode(",", $mybb->settings['dvz_sb_bot_forum_ignore']);

        if (!in_array($post['fid'], $forumIgnore) && !in_array("-1", $forumIgnore)) {
            $postMessage = new \Qwizi\DVZSB\Messages\PostMessage($mybb->settings['dvz_sb_bot_post_message']);
            $postLink = get_post_link($post['pid'], $post['tid']);
            $postLinkPid = $postLink . '#pid' . $post['pid'];
            $postTitle = htmlspecialchars_uni($post['subject']);
            $postFullLink = $postMessage->createLink(
                $mybb->settings['bburl'],
                $postLinkPid,
                $postTitle
            );

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

            $message = $postMessage->convert([
                'username' => $post['username'],
                'subject' => $postFullLink,
                'message' => $post['message'],
                'pid' => $post['pid'],
                'dateline' => $post['dateline'],
            ]);
            \Qwizi\DVZSB\Bot::i()->shout($message);
        }
    }
}

function dvz_shoutbox_bot_shout_commit(&$data)
{
    global $db, $mybb, $lang, $plugins;

    dvz_shoutbox_bot_create_instances();

    if ($mybb->settings['dvz_sb_bot_commands_onoff'] && dvz_shoutbox_bot_shout_startsWith($data['text'], $mybb->settings['dvz_sb_bot_commands_prefix'])) {
        $explodeData = explode(" ", $data['text']);
        $subData = substr($explodeData[0], 1);
        $command = \Qwizi\DVZSB\CommandManager::i()->getCommandByCommand($subData);
        if (!empty($command)) {
            $lang->load("dvz_shoutbox_bot_{$command['tag']}");

            $nameSpace = 'Qwizi\\DVZSB\\Commands\\';
            $commandClassName = $nameSpace . ucfirst($command['tag']) . 'Cmd';

            try {
                if (!class_exists($commandClassName)) {
                    throw new Exception('Class ' . $commandClassName . " not exists", 404);
                }

                $command['prefix'] = $mybb->settings['dvz_sb_bot_commands_prefix'];
                $commandClass = new $commandClassName($data, $command);

                if ($commandClass instanceof \Qwizi\DVZSB\AbstractCommand) {
                    if ($commandClass instanceof \Qwizi\DVZSB\Commands\ModRequiredInterface && !\Qwizi\DVZSB\Bot::i()->accessMod()) {
                        return;
                    }

                    $action = new \Qwizi\DVZSB\CommandAction();

                    $action->add(
                        'ban',
                        new \Qwizi\DVZSB\Actions\BanAction($mybb, $db)
                    )
                    ->add(
                        'log',
                        new \Qwizi\DVZSB\Actions\LogAction($db, $command['tag'])
                    )
                    ->add(
                        'mention',
                        new \Qwizi\DVZSB\Actions\MentionUserAction()
                    )
                    ->add(
                        'paginate',
                        new \Qwizi\DVZSB\Actions\PaginateAction()
                    );

                    $plugins->run_hooks('dvz_shoutbox_bot_action_end', $action);

                    $validator = new \Qwizi\DVZSB\CommandValidator($lang);

                    $validator->add(
                        'user',
                        new \Qwizi\DVZSB\Validators\IsUserValidator($validator->getLang())
                    )
                    ->add(
                        'super_admin',
                        new \Qwizi\DVZSB\Validators\IsSuperAdminValidator($validator->getLang())
                    )
                    ->add(
                        'interger',
                        new \Qwizi\DVZSB\Validators\IsIntegerValidator($validator->getLang())
                    )
                    ->add(
                        'float',
                        new \Qwizi\DVZSB\Validators\IsFloatValidator($validator->getLang())
                    )
                    ->add(
                        'not_empty_argument',
                        new \Qwizi\DVZSB\Validators\IsNotEmptyArgumentValidator($validator->getLang())
                    );
                    
                    $plugins->run_hooks('dvz_shoutbox_bot_validator_end', $validator);

                    $commandClass->setBot(\Qwizi\DVZSB\Bot::i())
                                ->setValidator($validator)
                                ->setAction($action)
                                ->handle();
                }
            } catch (Exception $e) {
                echo 'Error message: ' . $e->getMessage();
            }
        }
    }
}


function dvz_shoutbox_bot_create_bot_instance()
{
    global $mybb, $db;
    \Qwizi\DVZSB\Bot::createInstance($mybb, $db);
}

function dvz_shoutbox_bot_create_command_manager_instance()
{
    global $db, $cache;
    \Qwizi\DVZSB\CommandManager::createInstance($db, $cache);
}

function dvz_shoutbox_bot_create_instances()
{
    dvz_shoutbox_bot_create_bot_instance();
    dvz_shoutbox_bot_create_command_manager_instance();
}

$plugins->add_hook('index_end', 'dvz_sb_index');

function dvz_sb_index()
{
    dvz_shoutbox_bot_create_instances();
    $test = 'test';
    $test2 = '/test';

    $nameSpace = ['core', '\\Qwizi\\DVZSB\\Commands\\'];

    $commandData = [
        [
            'tag' => 'ban',
            'name' => 'Ban',
            'command' => 'ban',
            'description' => 'This command allows you to ban users',
            'activated' => 1
        ]
    ];

    $commandDataForCache = [];
    
    $commandDataForCache2 = [];
    foreach($commandData as $command) {
        if (!key_exists('file', $command)) {
            $command['file'] = $nameSpace[1].ucfirst($command['tag']). 'Cmd';
        }
        $commandDataForCache2[$nameSpace[0]] = $command;
    }

    var_dump($commandDataForCache2);
}

function dvz_shoutbox_bot_shout_startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}
