<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Admin\Commands;

use \Qwizi\Core\Admin\Action;
use \Qwizi\Core\Admin\Form;
use \MyBB;

class EditAction extends Action
{
    public function post(){
        global $mybb, $db;
        if (!trim($mybb->input['name'])) $this->setError('Pole name nie moze byc puste');
        if (!trim($mybb->input['description'])) $this->setError('Pole description nie moze byc puste');
        if (!trim($mybb->input['command'])) $this->setError('Pole command nie moze byc puste');

        if (empty($this->getErrors())) {
            $updated_command = [
                'name' => $db->escape_string($mybb->input['name']),
                'description' => $db->escape_string($mybb->input['description']),
                'command' => $db->escape_string($mybb->input['command']),
                'activated' => (int) $mybb->input['activated'],
            ];
            $cid = $mybb->get_input('cid', MyBB::INPUT_INT);
            $db->update_query('dvz_shoutbox_bot_commands', $updated_command, "cid=\"" . $cid . "\"");

            \flash_message('Successfully updated command', 'success');
            \admin_redirect($this->module_link);
        }
    }

    public function get() {
        global $mybb, $db;
        $query = $db->simple_select('dvz_shoutbox_bot_commands', "*", "cid=\"" . $mybb->get_input('cid', MyBB::INPUT_INT) . "\"");
        $command = $db->fetch_array($query);

        foreach($command as $key => $value) {
            $value = \htmlspecialchars_uni($value);
        }

        if (!$command['cid']) {
            \flash_message('Command not found', 'error');
            \admin_redirect($this->module_link);
        }

        $form = new Form($this->module_link."&amp;action={$this->action}&amp;cid={$command['cid']}", 'post', 'Edit command');
        $form
            ->row('name', 'Name', 'Name', [
                'type' => Form::INPUT_TEXT,
                'value' => $command['name']
            ])
            ->row('description', 'Description', 'description', [
                'type' => Form::INPUT_TEXT,
                'value' => $command['description']
            ])
            ->row('command', 'command', 'command', [
                'type' => Form::INPUT_TEXT,
                'value' => $command['command']
            ])
            ->row('activated', 'Activated', 'activated', [
                'type' => Form::INPUT_SELECTBOX,
                'option_list' => ['1' => 'Yes', '0' => 'No'],
                'value' => $command['activated']
            ])
            ;

        $this->addForm($form);
    }
}
