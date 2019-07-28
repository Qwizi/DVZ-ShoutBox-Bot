<?php

if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

if (!defined("PLUGINLIBRARY")) {
    define("PLUGINLIBRARY", MYBB_ROOT . "inc/plugins/pluginlibrary.php");
}

define("MODULE_LINK", 'index.php?module=user-dvz-shoutbox-bot');

$page->add_breadcrumb_item('Komendy', MODULE_LINK);

if ($mybb->input['action'] == 'add' || !$mybb->input['action']) {
    $sub_tabs['manage_commands'] = [
        'title' => 'Zarządzaj komendami',
        'link' => MODULE_LINK,
        'description' => 'Zarzadzaj komendami',
    ];

    $sub_tabs['add_command'] = [
        'title' => 'Dodaj komende',
        'link' => MODULE_LINK . '&amp;action=add',
        'description' => 'Zarzadzaj komendami',
    ];
}

$plugins->run_hooks("admin_dvz_shoutbox_bot_begin");

if ($mybb->input['action'] == 'add') {
    $plugins->run_hooks("admin_dvz_shoutbox_bot_add");

    if ($mybb->request_method == 'post') {

        if (!trim($mybb->input['name'])) {
            $errors[] = 'Nieprawidłowa nazwa';
        }

        if (!trim($mybb->input['description'])) {
            $errors[] = 'Nieprawidłowy opis';
        }

        if (!trim($mybb->input['tag'])) {
            $errors[] = 'Nieprawidłwy tag';
        }

        if (!trim($mybb->input['command'])) {
            $errors[] = 'Nieprawidłowa komenda';
        }

        if (!$errors) {
            $new_command = [
                'name' => $db->escape_string($mybb->input['name']),
                'description' => $db->escape_string($mybb->input['description']),
                'tag' => $db->escape_string($mybb->input['tag']),
                'command' => $db->escape_string($mybb->input['command']),
                'activated' => 1
            ];

            $plugins->run_hooks("admin_dvz_shoutbox_bot_add_commit");

            $cid = $db->insert_query('dvz_shoutbox_bot_commands', $new_command);

            $PL or require_once PLUGINLIBRARY;

            $pluginCache = $PL->cache_read('dvz_shoutbox_bot');

            array_push($pluginCache['commands'], $new_command);

            $PL->cache_update('dvz_shoutbox_bot', ['commands' => $pluginCache['commands']]);

            $plugins->run_hooks("admin_dvz_shoutbox_bot_add_commit_end", $cid);

            flash_message('Pomyślnie dodano komende', 'success');
			admin_redirect(MODULE_LINK);
        }
    }

    $page->output_header('Dodaj komende');
    $page->output_nav_tabs($sub_tabs, 'add_command');

    $form = new Form(MODULE_LINK.'&amp;action=add', 'post');

    $form_container = new FormContainer('Dodaj komende');
    $form_container->output_row("Nazwa <em>*</em>", "", $form->generate_text_box('name', $mybb->input['name'], ['id' => 'name'], 'name'));
    $form_container->output_row("Opis <em>*</em>", "", $form->generate_text_area('description', $mybb->input['description'], ['id' => 'description'], 'description'));
    $form_container->output_row("Tag <em>*</em>", "", $form->generate_text_box('tag', $mybb->input['tag'], ['id' => 'tag'], 'tag'));
    $form_container->output_row("Komenda <em>*</em>", "", $form->generate_text_box('command', $mybb->input['command'], ['id' => 'command'], 'command'));
    // $form_container->output_row("Nazwa <em>*</em>", "", $form->generate_text_box('name', $mybb->input['name'], ['id' => 'name'], 'name'));

    $form_container->construct_row();

    $form_container->end();

    $buttons = [];
    $buttons[] = $form->generate_submit_button('Zapisz komende');
    $form->output_submit_wrapper($buttons);
    $form->end();

    $page->output_footer();
}

if ($mybb->input['action'] == 'edit') {

    $query = $db->simple_select('dvz_shoutbox_bot_commands',  "*", "cid=\"".$mybb->get_input('cid', MyBB::INPUT_INT)."\"");
    $command = $db->fetch_array($query);

    if (!$command['cid']) {
        flash_message('Nie znaleziono komendy', 'error');
        admin_redirect(MODULE_LINK);
    }

    $plugins->run_hooks("admin_dvz_shoutbox_bot_edit");

    if ($mybb->request_method == 'post') {
        if (!trim($mybb->input['name'])) {
            $errors[] = 'Nieprawidłowa nazwa';
        }

        if (!trim($mybb->input['description'])) {
            $errors[] = 'Nieprawidłowy opis';
        }

        if (!trim($mybb->input['tag'])) {
            $errors[] = 'Nieprawidłwy tag';
        }

        if (!trim($mybb->input['command'])) {
            $errors[] = 'Nieprawidłowa komenda';
        }

        if (!$errors) {
            $updated_command = [
                'name' => $db->escape_string($mybb->input['name']),
                'description' => $db->escape_string($mybb->input['description']),
                'tag' => $db->escape_string($mybb->input['tag']),
                'command' => $db->escape_string($mybb->input['command']),
                'activated' => 1
            ];

            $plugins->run_hooks("admin_dvz_shoutbox_bot_edit_commit");

            $cid = $db->update_query('dvz_shoutbox_bot_commands', $updated_command, "cid=\"".$mybb->get_input('cid', MyBB::INPUT_INT)."\"");

            $PL or require_once PLUGINLIBRARY;

            $pluginCache = $PL->cache_read('dvz_shoutbox_bot');

            $searchKey = array_search($mybb->input['name'], $pluginCache['commands']);

            array_splice($pluginCache['commands'], $searchKey);

            $PL->cache_update('dvz_shoutbox_bot', ['commands' => $pluginCache['commands']]);

            $plugins->run_hooks("admin_dvz_shoutbox_bot_add_commit_end", $cid);
        }
    }
    
    if ($errors) {
        $page->output_inline_error($errors);
    } else {
        $mybb->input = array_merge($mybb->input, $command);
    }

    $page->output_header('Edytuj komende');

    $sub_tabs = [];
    $sub_tabs['edit_command'] = [
        'title' => 'Edytuj komende',
        'description' => 'Edytuj komende',
    ];
    $page->output_nav_tabs($sub_tabs, 'edit_command');

    $form = new Form(MODULE_LINK.'&amp;action=edit&amp;cid={$', 'post');

    $form_container = new FormContainer('Edytuj komende komende');
    $form_container->output_row("Nazwa <em>*</em>", "", $form->generate_text_box('name', $mybb->input['name'], ['id' => 'name'], 'name'));
    $form_container->output_row("Opis <em>*</em>", "", $form->generate_text_area('description', $mybb->input['description'], ['id' => 'description'], 'description'));
    $form_container->output_row("Tag <em>*</em>", "", $form->generate_text_box('tag', $mybb->input['tag'], ['id' => 'tag'], 'tag'));
    $form_container->output_row("Komenda <em>*</em>", "", $form->generate_text_box('command', $mybb->input['command'], ['id' => 'command'], 'command'));
    $form_container->output_row("Aktywna", "", $form->generate_check_box("activated", 1, 'Aktywna', ["checked" => $mybb->input['activated']]));

    $form_container->construct_row();

    $form_container->end();

    $buttons = [];
    $buttons[] = $form->generate_submit_button('Zapisz komende');
    $form->output_submit_wrapper($buttons);
    $form->end();

    $page->output_footer();
}

if (!$mybb->input['action']) {
    $plugins->run_hooks("admin_dvz_shoutbox_bot_start");

    // TODO Dodać metode post

    $page->output_header('Zarządzaj komendami');
    $page->output_nav_tabs($sub_tabs, 'manage_commands');

    //TODO ZAPYTANIA i FORMULARZ
    $form = new Form(MODULE_LINK, 'post', 'dvz-shoutbox-bot');

    $form_container = new FormContainer('Zarządzaj komendami');
    $form_container->output_row_header('Nazwa');
    $form_container->output_row_header('Opis');
    $form_container->output_row_header('Aktywna', ['class' => 'align_center']);
    $form_container->output_row_header('Opcje', ['class' => 'align_center']);

    $query = $db->simple_select('dvz_shoutbox_bot_commands', 'cid, name, description, activated');

    if ((bool) $db->num_rows($query)) {
        while ($row = $db->fetch_array($query)) {
            $form_container->output_cell($row['name']);
            $form_container->output_cell($row['description']);

            $form_container->output_cell($row['activated'] == 1 ? 'tak' : 'nie', ['class' => 'align_center']);

            $popup = new PopupMenu("command_{$row['cid']}", 'Opcje');
            $popup->add_item('Edytuj', MODULE_LINK . "&amp;action=edit&amp;cid={$row['cid']}");
            $popup->add_item('Usuń', MODULE_LINK . "&amp;action=delete&amp;cid={$row['cid']}");

            $form_container->output_cell($popup->fetch(), ['class' => 'align_center']);

            $form_container->construct_row();
        }
    }

    if ($form_container->num_rows() == 0) {
        $form_container->output_cell('Brak komend', array('colspan' => 5));
        $form_container->construct_row();
    }

    $form_container->end();

    $form->end();

    $page->output_footer();
}
