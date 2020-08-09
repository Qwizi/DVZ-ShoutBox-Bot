<?php
defined('IN_MYBB') or die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');

function dvzsbbot_meta()
{
    global $page, $lang, $plugins;

    $sub_menu = [];
    $sub_menu['10'] = ['id' => 'dvzsbbot', 'title' => 'Commands', 'link' => 'index.php?module=dvzsbbot'];

    $sub_menu = $plugins->run_hooks('admin_dvzsbbot_menu', $sub_menu);


    $page->add_menu_item('DVZ ShoutBox Bot', 'dvzsbbot', 'index.php?module=dvzsbbot', 60, $sub_menu);
    return true;
}

function dvzsbbot_action_handler($action)
{
    global $page, $lang, $plugins;

    $page->active_module = 'dvzsbbot';

    $actions = [
        'commands' => ['active' => 'commands', 'file' => 'commands.php'],
	];

	$actions = $plugins->run_hooks("admin_dvzsbbot_action_handler", $actions);

	if(isset($actions[$action]))
	{
		$page->active_action = $actions[$action]['active'];
		return $actions[$action]['file'];
	}
	else
	{
		$page->active_action = "commands";
		return "commands.php";
	}
}