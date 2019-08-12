<?php

use Qwizi\DVZSB\Command;

if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

define("MODULE_LINK", 'index.php?module=user-dvz-shoutbox-bot');

$lang->load('dvz_shoutbox_bot');

$page->add_breadcrumb_item('DVZ ShoutBox Bot', MODULE_LINK);

if ($mybb->input['action'] == 'add' || !$mybb->input['action']) {

    $sub_tabs = [
        'manage_commands' => [
            'title' => $lang->manage_commands_t,
            'link' => MODULE_LINK,
            'description' => $lang->manage_commands_d,
        ],
        'reload_commands' => [
            'title' => $lang->reload_commands_t,
            'link' => MODULE_LINK . '&amp;action=reload',
            'description' => $lang->reload_commands_d,
        ]
    ];
}

$plugins->run_hooks("admin_dvz_shoutbox_bot_begin");

if ($mybb->input['action'] == 'edit') {

    $query = $db->simple_select('dvz_shoutbox_bot_commands', "*", "cid=\"" . $mybb->get_input('cid', MyBB::INPUT_INT) . "\"");
    $commandQ = $db->fetch_array($query);

    if (!$commandQ['cid']) {
        flash_message($lang->command_not_found, 'error');
        admin_redirect(MODULE_LINK);
    }

    $plugins->run_hooks("admin_dvz_shoutbox_bot_edit");

    if ($mybb->request_method == 'post') {
        if (!trim($mybb->input['name'])) {
            $errors[] = $lang->row_m_name;
        }

        if (!trim($mybb->input['description'])) {
            $errors[] = $lang->row_m_description;
        }

        if (!trim($mybb->input['command'])) {
            $errors[] = $lang->row_m_command;
        }

        if (!$errors) {
            $updated_command = [
                'name' => $db->escape_string($mybb->input['name']),
                'description' => $db->escape_string($mybb->input['description']),
                'command' => $db->escape_string($mybb->input['command']),
                'activated' => (int) $mybb->input['activated'],
            ];

            $plugins->run_hooks("admin_dvz_shoutbox_bot_edit_commit");

            $cid = $db->update_query('dvz_shoutbox_bot_commands', $updated_command, "cid=\"" . $mybb->get_input('cid', MyBB::INPUT_INT) . "\"");

            require_once MYBB_ROOT . '/inc/plugins/QwiziPlugins/DVZSB/src/Command.php';

            Command::createInstance($cache, $db);

            Command::i()->updateCache();

            $plugins->run_hooks("admin_dvz_shoutbox_bot_add_commit_end", $cid);

            flash_message($lang->edit_command_success_message, 'success');
            admin_redirect(MODULE_LINK);
        }
    }

    $page->output_header($lang->edit_command_t);

    $sub_tabs = [
        'edit_command' => [
            'title' => $lang->edit_command_t,
            'description' => $lang->edit_command_d,
        ]
    ];
    $page->output_nav_tabs($sub_tabs, 'edit_command');

    if ($errors) {
        $page->output_inline_error($errors);
    } else {
        $mybb->input = array_merge($mybb->input, $commandQ);
    }

    $form = new Form(MODULE_LINK . "&amp;action=edit&amp;cid={$commandQ['cid']}", 'post');

    $form_container = new FormContainer($lang->edit_command_t);
    $form_container->output_row($lang->row_name_t."<em>*</em>", $lang->row_name_d, $form->generate_text_box('name', $mybb->input['name'], ['id' => 'name'], 'name'));
    $form_container->output_row($lang->row_description_t."<em>*</em>", $lang->row_description_d, $form->generate_text_area('description', $mybb->input['description'], ['id' => 'description'], 'description'));
    $form_container->output_row($lang->row_command_t."<em>*</em>", $lang->row_command_d, $form->generate_text_box('command', $mybb->input['command'], ['id' => 'command'], 'command'));
    $form_container->output_row($lang->row_activated_t, $lang->row_activated_d, $form->generate_check_box("activated", 1, 'Aktywna', ["checked" => $mybb->input['activated']]));

    $form_container->construct_row();

    $form_container->end();

    $buttons = [];
    $buttons[] = $form->generate_submit_button($lang->save);
    $form->output_submit_wrapper($buttons);
    $form->end();

    $page->output_footer();
}

if ($mybb->input['action'] == 'delete') {
    $query = $db->simple_select('dvz_shoutbox_bot_commands', "*", "cid=\"" . $mybb->get_input('cid', MyBB::INPUT_INT) . "\"");
    $commandQ = $db->fetch_array($query);

    if (!$commandQ['cid']) {
        flash_message($lang->command_not_found, 'error');
        admin_redirect(MODULE_LINK);
    }

    if ($mybb->input['no']) {
        admin_redirect(MODULE_LINK);
    }

    $plugins->run_hooks("admin_dvz_shoutbox_bot_delete");

    if ($mybb->request_method == 'post') {
        $db->delete_query('dvz_shoutbox_bot_commands', "cid='{$commandQ['cid']}'");

        require_once MYBB_ROOT . '/inc/plugins/QwiziPlugins/DVZSB/src/Command.php';

        Command::createInstance($cache, $db);

        Command::i()->updateCache();

        admin_redirect(MODULE_LINK);
    } else {
        $page->output_confirm_action(MODULE_LINK . "&amp;action=delete&amp;cid={$commandQ['cid']}", $lang->delete_question);
    }
}

if ($mybb->input['action'] == 'reload') {
    $plugins->run_hooks("admin_dvz_shoutbox_bot_reload");
}

if (!$mybb->input['action']) {
    $plugins->run_hooks("admin_dvz_shoutbox_bot_start");

    // TODO Dodać metode post

    $page->output_header($lang->manage_commands_t);
    $page->output_nav_tabs($sub_tabs, 'manage_commands');

    //TODO ZAPYTANIA i FORMULARZ
    $form = new Form(MODULE_LINK, 'post', 'dvz-shoutbox-bot');

    $form_container = new FormContainer($lang->manage_commands_t);
    $form_container->output_row_header($lang->row_name_t);
    $form_container->output_row_header($lang->row_description_t);
    $form_container->output_row_header($lang->row_activated_t, ['class' => 'align_center']);
    $form_container->output_row_header($lang->row_options, ['class' => 'align_center']);

    $query = $db->simple_select('dvz_shoutbox_bot_commands', 'cid, name, description, activated');

    if ((bool) $db->num_rows($query)) {
        while ($row = $db->fetch_array($query)) {
            $form_container->output_cell($row['name']);
            $form_container->output_cell($row['description']);

            $form_container->output_cell($row['activated'] == 1 ? $lang->row_activated_y : $lang->row_activated_n, ['class' => 'align_center']);

            $popup = new PopupMenu("command_{$row['cid']}", 'Opcje');
            $popup->add_item($lang->row_options_e, MODULE_LINK . "&amp;action=edit&amp;cid={$row['cid']}");
            $popup->add_item($lang->row_options_d, MODULE_LINK . "&amp;action=delete&amp;cid={$row['cid']}");

            $form_container->output_cell($popup->fetch(), ['class' => 'align_center']);

            $form_container->construct_row();
        }
    }

    if ($form_container->num_rows() == 0) {
        $form_container->output_cell($lang->row_empty, array('colspan' => 5));
        $form_container->construct_row();
    }

    $form_container->end();

    $form->end();

    $page->output_footer();
}
