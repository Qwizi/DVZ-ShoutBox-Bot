<?php
if(!defined("IN_MYBB"))
{
	header("HTTP/1.0 404 Not Found");
	exit;
}
$page->add_breadcrumb_item($lang->bot_title, "index.php?module=user-dvz_shoutbox_bot");

if($mybb->input['action'] == 'add' || $mybb->input['action'] == 'edit' || !$mybb->input['action'])
{
	$sub_tabs['dvz_shoutbox_bot_view'] = array(
			'title'			=> $lang->bot_panel_view_title,
			'link'		  => 'index.php?module=user-dvz_shoutbox_bot',
			'description'	=> $lang->bot_panel_view_desc,
	);

	$sub_tabs['dvz_shoutbox_bot_add'] = array(
			'title'			=> $lang->bot_panel_add_title,
			'link'		  => 'index.php?module=user-dvz_shoutbox_bot&amp;action=add',
			'description'	=> $lang->bot_panel_add_desc,
	);
}

if(!$mybb->input['action'])
{
	$page->output_header($lang->bot_title);

	$page->output_nav_tabs($sub_tabs, 'dvz_shoutbox_bot_view');

	$table = new Table;
	$table->construct_header($lang->bot_panel_message, ['width' => '45%']);
	$table->construct_header($lang->bot_panel_answer, ['width' => '45%']);
	$table->construct_header($lang->bot_panel_options, ['width' => '10%', 'class' => 'align_center']);

	$query = $db->simple_select("dvz_shoutbox_bot", '*', '', array('order_by' => 'id'));
	while($row = $db->fetch_array($query))
	{
		$table->construct_cell($row['string']);
		$table->construct_cell($row['answer']);
		$popup = new PopupMenu("dvz_shoutbox_bot_{$row['id']}", $lang->bot_panel_options);
		$popup->add_item($lang->bot_panel_edit_title, "index.php?module=user-dvz_shoutbox_bot&amp;action=edit&amp;id={$row['id']}");
		$popup->add_item($lang->bot_panel_delete_title, "index.php?module=user-dvz_shoutbox_bot&amp;action=delete&amp;id={$row['id']}");
		$table->construct_cell($popup->fetch(), ['class' => 'align_center']);
		$table->construct_row();
	}
	if($table->num_rows()  == 0)
	{
		$table->construct_cell($lang->bot_panel_nomessage, ['colspan' => 6]);
		$table->construct_row();
	}
	$table->output("DVZ ShoutBox Bot");
	$page->output_footer();
}
elseif($mybb->input['action'] == 'add')
{
	if($mybb->request_method == "post")
	{
		if(empty($mybb->input['string']) || empty($mybb->input['answer']))
		{
			flash_message($lang->bot_panel_empty_fields, "error");
			admin_redirect("index.php?module=user-dvz_shoutbox_bot&amp;action=add");
		}
		if(strpos($mybb->input['string'], "'") !== false)
		{
			flash_message($lang->bot_panel_characters_m, "error");
			admin_redirect("index.php?module=user-dvz_shoutbox_bot&amp;action=add");
		}
		if(strpos($mybb->input['answer'], "'") !== false)
		{
			flash_message($lang->bot_panel_characters_a, "error");
			admin_redirect("index.php?module=user-dvz_shoutbox_bot&amp;action=add");
		}
		$query = array(
			"string"	=>  $db->escape_string($mybb->input['string']),
			"answer"	=> $db->escape_string($mybb->input['answer'])
		);
		$db->insert_query("dvz_shoutbox_bot", $query);
		flash_message($lang->bot_panel_action_success, "success");
		admin_redirect("index.php?module=user-dvz_shoutbox_bot");
	}
	else
	{
		$page->output_header($lang->bot_title." - ".$lang->bot_panel_add_title);
		$page->add_breadcrumb_item($lang->bot_panel_add_title);

	    $sub_tabs['dvz_shoutbox_bot_add'] = array(
	        'title' => $lang->bot_panel_add_title,
	        'link' => "index.php?module=user-dvz_shoutbox_bot&amp;action=add",
	        'description' => $lang->bot_panel_add_desc,
	    );

		$page->output_nav_tabs($sub_tabs, 'dvz_shoutbox_bot_add');

		$form = new Form("index.php?module=user-dvz_shoutbox_bot&amp;action=add", "post", "dvz_shoutbox_bot");

		$form_container = new FormContainer($lang->bot_panel_add_title);
		$form_container->output_row($lang->bot_panel_message, $lang->bot_panel_message_desc, $form->generate_text_area('string', '', array('id' => 'string'), 'string'));
		$form_container->output_row($lang->bot_panel_answer, $lang->bot_panel_answer_desc, $form->generate_text_area('answer', '', array('id' => 'answer'), 'answer'));
		$form_container->end();
		$buttons = array();
		$buttons[] = $form->generate_submit_button($lang->bot_panel_add_title);
		$form->output_submit_wrapper($buttons);
		$form->end();
		$page->output_footer();
	}
}
elseif($mybb->input['action'] == 'edit')
{
	if($mybb->request_method == "post")
	{
		$query = $db->simple_select("dvz_shoutbox_bot", "*", "id=\"".intval($mybb->input['id'])."\"");
		$row = $db->fetch_array($query);
		if(!$row)
		{
			flash_message("Nie ma pola o takim id", "error");
			admin_redirect("index.php?module=user-dvz_shoutbox_bot");
		}
		if(empty($mybb->input['string']) || empty($mybb->input['answer']))
		{
			flash_message($lang->bot_panel_empty_fields, "error");
			admin_redirect("index.php?module=user-dvz_shoutbox_bot&amp;action=edit&amp;id={$row['id']}");
		}
		if(strpos($mybb->input['string'], "'") !== false)
		{
			flash_message($lang->bot_panel_characters_m, "error");
			admin_redirect("index.php?module=user-dvz_shoutbox_bot&amp;action=edit&amp;id={$row['id']}");
		}
		if(strpos($mybb->input['answer'], "'") !== false)
		{
			flash_message($lang->bot_panel_characters_a, "error");
			admin_redirect("index.php?module=user-dvz_shoutbox_bot&amp;action=edit&amp;id={$row['id']}");
		}

		$update_query = array(
			"string"	=> $db->escape_string($mybb->input['string']),
			"answer" => $db->escape_string($mybb->input['answer']),
		);
		$db->update_query("dvz_shoutbox_bot", $update_query, 'id='.$row['id']);
		flash_message($lang->bot_panel_action_edit_success, "success");
		admin_redirect("index.php?module=user-dvz_shoutbox_bot");
	}
	else
	{
		$page->output_header($lang->bot_title." - ".$lang->bot_panel_edit_title);
		$page->add_breadcrumb_item($lang->bot_panel_edit_title);

		$sub_tabs['dvz_shoutbox_bot_edit'] = array(
			 'title' => $lang->bot_panel_edit_title,
			 'link' => "index.php?module=user-dvz_shoutbox_bot&amp;action=edit",
			 'description' => $lang->bot_panel_edit_desc,
		);
		$page->output_nav_tabs($sub_tabs, 'dvz_shoutbox_bot_edit');

		if(!$mybb->input['id'])
		{
			admin_redirect("index.php?module=user-dvz_shoutbox_bot");
		}

		$query = $db->simple_select("dvz_shoutbox_bot", "*", "id=\"".intval($mybb->input['id'])."\"");
		$row = $db->fetch_array($query);

		$form = new Form("index.php?module=user-dvz_shoutbox_bot&amp;action=edit", "post", "dvz_shoutbox_bot");

		echo $form->generate_hidden_field("id", $row['id']);

		$form_container = new FormContainer($lang->bot_panel_edit_title);
		$form_container->output_row($lang->bot_panel_message, $lang->bot_panel_message_desc, $form->generate_text_area("string", $row['string']), array("id" => "string", "string"));
		$form_container->output_row($lang->bot_panel_answer, $lang->bot_panel_answer_desc, $form->generate_text_area("answer", $row['answer']), array("id" => "answer", "answer"));
		$form_container->end();

		$buttons = array();
		$buttons[] = $form->generate_submit_button($lang->bot_panel_edit_title);
		$form->output_submit_wrapper($buttons);
		$form->end();
		$page->output_footer();
	}
}
elseif($mybb->input['action'] == 'delete')
{
	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=user-dvz_shoutbox_bot");
	}
	if($mybb->request_method == "post")
	{
		$db->delete_query("dvz_shoutbox_bot", "id=".intval($mybb->input['id'])."");
		admin_redirect("index.php?module=user-dvz_shoutbox_bot");
	}
	else
	{
		$page->output_header($lang->bot_title." - ".$lang->bot_panel_delete_title);
		$page->add_breadcrumb_item($lang->bot_panel_delete_title);

		$mybb->input['id'] = intval($mybb->input['id']);
		$form = new Form("index.php?module=user-dvz_shoutbox_bot&amp;action=delete&amp;id={$mybb->input['id']}", 'post');
		echo "<div class=\"confirm_action\">\n";
		echo "<p>{$lang->bot_panel_delete}</p>\n";
		echo "<br />\n";
		echo "<p class=\"buttons\">\n";
		echo $form->generate_submit_button($lang->bot_panel_yes, array('class' => 'button_yes'));
		echo $form->generate_submit_button($lang->bot_panel_no, array("name" => "no", 'class' => 'button_no'));
		echo "</p>\n";
		echo "</div>\n";
		$form->end();
		$page->output_footer();
	}
}
exit;
?>
