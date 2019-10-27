<?php
defined('IN_MYBB') or die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');

defined('QWIZI_PLUGINS_CORE_PATH') || define('QWIZI_PLUGINS_CORE_PATH', __DIR__ . '/QwiziPlugins/Core');

define('TEST_PLUGIN_PATH', __DIR__ . '/QwiziPlugins/TestCommands/');

require_once QWIZI_PLUGINS_CORE_PATH . '/src/ClassLoader.php';

$classLoader = \Qwizi\Core\ClassLoader::getInstance();
$classLoader->registerNamespace(
    'Qwizi\\TestCommands\\',
    TEST_PLUGIN_PATH . '/src/'
)->register();

function test_plugin_info()
{
    return [
        'name' => 'Test',
        'description' => 'Test',
        'author' => 'Adrian \'Qwizi\' Ciołek',
        'authorsite' => 'https://github.com/Qwizi',
        'version' => '1.0.0',
        'compatibility' => '18*',
    ];
}

function test_plugin_activate()
{
    if (function_exists('dvz_shoutbox_bot_create_command_manager_instance')) {
        dvz_shoutbox_bot_create_command_manager_instance();

        \Qwizi\DVZSB\CommandManager::i()->createCommand(
            '//Qwizi//TestCommands//Commands//',
            [
                [
                    'tag' => 'test',
                    'name' => 'Test',
                    'command' => 'test',
                    'description' => 'Test',
                    'activated' => 1
                ]
            ]
        );
    }
}

function test_plugin_deactivate()
{
    global $db;

    $db->delete_query('dvz_shoutbox_bot_commands', 'tag=\'test\'');
}

/*$plugins->add_hook('dvz_shoutbox_bot_actions_end', 'test_plugin_actions_end');

 function test_plugin_actions_end(&$action)
{
    $action->add(
        'test',
        new \Qwizi\TestCommands\Actions\TestAction()
    );
} */
