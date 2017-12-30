<?php
if(!defined("IN_MYBB"))
{
	header("HTTP/1.0 404 Not Found");
	exit;
}
$page->add_breadcrumb_item("DVZ ShoutBox Bot", "index.php?module=user-dvz-shoutbox-bot");
$page->output_header("DVZ ShoutBox Bot");

$sub_tabs['dvz_shoutbox_view'] = array(
		'title'			=> 'Widok',
		'link'		  => 'index.php?module=user-dvz-shoutbox-bot',
		'description'	=> 'Widok',
);

$sub_tabs['dvz_shoutbox_add'] = array(
		'title'			=> 'Dodaj',
		'link'		  => 'index.php?module=user-dvz-shoutbox-bot&amp;action=add',
		'description'	=> "",
);

switch ($mybb->input['action'])
{
	case 'add':
	$page->output_nav_tabs($sub_tabs, 'dvz_shoutbox_add');
	break;
	default:
	$page->output_nav_tabs($sub_tabs, 'dvz_shoutbox_bot_view');
	break;
}

if (!$mybb->input['action'])
{
	$table = new Table;
	$table->construct_header("Id", array('width' => '1%'));
	$table->construct_header("Wiadomość", array('width' => '40%'));
	$table->construct_header("Odpowiedź", array('width' => '40%'));
	$table->construct_header("Opcje", array('width' => '10%'));

	$query = $db->simple_select("dvz_shoutbox_bot", '*', '', array('order_by' => 'id'));
	while($row = $db->fetch_array($query))
	{
		$table->construct_cell($row['id']);
		$table->construct_cell($row['string']);
		$table->construct_cell($row['answer']);
		$table->construct_cell("<a href=\"index.php?module=user-dvz-shoutbox-bot&amp;action=delete&amp;id={$row['id']}\">Usuń</a> - <a href=\"index.php?module=user-dvz-shoutbox-bot&amp;action=edit&amp;id={$row['id']}\">Edytuj</a>");
		$table->construct_row();
	}
	$table->output("DVZ ShoutBox Bot");
}
elseif($mybb->input['action'] == 'add')
{
	if($mybb->request_method == "post") // submit
	{
		if(empty($mybb->input['string']) || empty($mybb->input['answer']))
		{
			flash_message("Pola nie mogą być puste", "error");
			admin_redirect("index.php?module=user-dvz-shoutbox-bot&amp;action=add");
		}

		if(strpos($mybb->input['string'], "'") !== false)
		{
			flash_message("W wiadomości znajdują sie niedozwolone znaki", "error");
			admin_redirect("index.php?module=user-dvz-shoutbox-bot&amp;action=add");
		}

		if(strpos($mybb->input['answer'], "'") !== false)
		{
			flash_message("W odpowiedzi znajdują sie niedozwolone znaki", "error");
			admin_redirect("index.php?module=user-dvz-shoutbox-bot&amp;action=add");
		}
		$query = array(
			"id"		=> "",
			"string"	=> $mybb->input['string'],
			"answer"	=> $mybb->input['answer']
		);
		$db->insert_query("dvz_shoutbox_bot", $query);
		flash_message("Pomyślnie dodano wiadomość", "success");
		admin_redirect("index.php?module=user-dvz-shoutbox-bot");
	}
	else
	{
		$form = new Form("index.php?module=user-dvz-shoutbox-bot&amp;action=add", "post", "dvz-shoutbox-bot");

		$form_container = new FormContainer("Dodaj akcje");
		$form_container->output_row("Wiadomość", "Wiadomość", $form->generate_text_area('string', '', array('id' => 'string'), 'string'));
		$form_container->output_row("Odpowiedź", "Odpowiedz", $form->generate_text_area('answer', '', array('id' => 'answer'), 'answer'));
		$form_container->end();
		$buttons = array();
		$buttons[] = $form->generate_submit_button('Wyślij');
		$form->output_submit_wrapper($buttons);
		$form->end();
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
			admin_redirect("index.php?module=user-dvz-shoutbox-bot");
		}

		if(strpos($mybb->input['string'], "'") !== false)
		{
			flash_message("W wiadomości znajdują sie niedozwolone znaki", "error");
			admin_redirect("index.php?module=user-dvz-shoutbox-bot&amp;action=add");
		}

		if(strpos($mybb->input['answer'], "'") !== false)
		{
			flash_message("W odpowiedzi znajdują sie niedozwolone znaki", "error");
			admin_redirect("index.php?module=user-dvz-shoutbox-bot&amp;action=add");
		}

		$update_query = array(
			"string"	=> $mybb->input['string'],
			"answer" => $mybb->input['answer'],
		);
		$db->update_query("dvz_shoutbox_bot", $update_query, 'id='.$row['id']);
		flash_message("Pomyślnie z edytowano pole", "success");
		admin_redirect("index.php?module=user-dvz-shoutbox-bot");
	}
	else
	{
		$query = $db->simple_select("dvz_shoutbox_bot", "*", "id=\"".intval($mybb->input['id'])."\"");
		$row = $db->fetch_array($query);

		$form = new Form("index.php?module=user-dvz-shoutbox-bot&amp;action=edit", "post", "dvz-shoutbox-bot");

		echo $form->generate_hidden_field("id", $row['id']);

		$form_container = new FormContainer("Edytuj akcje");
		$form_container->output_row("Wiadmość", "Wiadomość", $form->generate_text_area("string", $row['string']), array("id" => "string", "string"));
		$form_container->output_row("Odpowiedź", "Odpowiedź", $form->generate_text_area("answer", $row['answer']), array("id" => "answer", "answer"));
		$form_container->end();

		$buttons = array();
		$buttons[] = $form->generate_submit_button("Edytuj");
		$form->output_submit_wrapper($buttons);
		$form->end();
	}
}
elseif($mybb->input['action'] == 'delete')
{
	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=user-dvz-shoutbox-bot");
	}
	if($mybb->request_method == "post")
	{
		$db->delete_query("dvz_shoutbox_bot", "id=".intval($mybb->input['id'])."");
		admin_redirect("index.php?module=user-dvz-shoutbox-bot");
	}
	else
	{
		$mybb->input['id'] = intval($mybb->input['id']);
		$form = new Form("index.php?module=user-dvz-shoutbox-bot&amp;action=delete&amp;id={$mybb->input['id']}", 'post');
		echo "<div class=\"confirm_action\">\n";
		echo "<p>Czy napewno chcesz usunąc?</p>\n";
		echo "<br />\n";
		echo "<p class=\"buttons\">\n";
		echo $form->generate_submit_button("Tak", array('class' => 'button_yes'));
		echo $form->generate_submit_button("Nie", array("name" => "no", 'class' => 'button_no'));
		echo "</p>\n";
		echo "</div>\n";
		$form->end();
	}
}

$page->output_footer();
exit;
?>
