<?php
function task_dvz_shoutbox_bot($task)
{
	global $mybb, $db;
    
    $db->delete_query('dvz_shoutbox', "text IS NULL");

	add_task_log($task, "Usunieto niepotrzebne wpisy");
}
