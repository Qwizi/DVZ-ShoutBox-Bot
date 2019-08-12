<?php
//! BOT ID
$l['id_t'] = 'Bot\'s ID';
$l['id_d'] = 'Enter the id of the user who will be sending the message in the chat.';

//! REGISTER ACTION SETTINGS
$l['register_t'] = 'Should the bot send a chat message when the user registers?';
$l['register_d'] = 'Determines wheter bot should be sending a message on chat if user will register.';
$l['register_message_t'] = 'Message sent to the chat, if the user registers';
$l['register_message_d'] = 'Message that will be sent by the bot. Use <b>{username}</b> to replace the login.';
$l['register_message_example'] = 'The player {username} has just registered. Welcome!';

//! THREAD ACTION SETTINGS
$l['thread_t'] = 'Should the bot send a chat message if the user will writes a new thread?';
$l['thread_d'] = 'Determines wheter bot should send message on chat if user write new thread.';
$l['thread_message_t'] = 'Message sent on chat if user will write new thread.';
$l['thread_message_d'] = 'Message that will be sent by the bot. Use <b>{username}</b> to replace the login, <b>{subject}</b> to get thread subject, <b>{forum}</b> to get name of forum';
$l['thread_message_example'] = 'New thread - {subject} in forum {forum}. [quote="{username}" pid="{pid}" dateline="{dateline}"]{message}[/quote]';

//! POST ACTION SETTINGS
$l['post_t'] = 'Should the bot send a chat message if the user will write a new post?';
$l['post_d'] = 'Determines wheter bot should sent message on chat if user will write new post.';
$l['post_message_t'] = 'Message sent to the chat, if the user writes a new post';
$l['post_message_d'] = 'Message that will be sent by the bot. Use <b>{username}</b> to replace the login, <b>{subject}</b> to get title of post.';
$l['post_message_example'] = 'New post - {subject}.[quote="{username}" pid="{pid}" dateline="{dateline}"]{message}[/quote]';

//! INGORE FORUMS
$l['ignore_t'] = "Ignoring forums";
$l['ignore_d'] = "Choose from list of that the bot should ignore";

//! COMMANDS
$l['commands_t'] = "Should the bot respond to commands?";
$l['commands_d'] = "Determines whether the bot should respond to commands.";
$l['commands_prefix_t'] = "Prefix for commands";
$l['commands_prefix_d'] = "Specifies the prefix for commands";

$l['manage_commands_t'] = "Manage commands";
$l['manage_commands_d'] = "Manage commands";
$l['reload_commands_t'] = "Reload commands";
$l['reload_commands_d'] = "Reload commands";
$l['edit_command_t'] = "Edit command";
$l['edit_command_d'] = "Edit command";

$l['row_name_t'] = "Name";
$l['row_name_d'] = "Name of the command";
$l['row_description_t'] = "Description";
$l['row_description_d'] = "Description of the command";
$l['row_command_t'] = "Command";
$l['row_command_d'] = "The command that will be entered in the chat";
$l['row_activated_t'] = "Active";
$l['row_activated_d'] = "Whether the command is to be active";
$l['row_activated_y'] = "yes";
$l['row_activated_n'] = "no";
$l['row_options'] = "Options";
$l['row_options_e'] = "Edit";
$l['row_options_d'] = "Delete";
$l['row_empty'] = "No commands";

$l['row_m_name'] = 'Missing name';
$l['row_m_description'] = "Missing description";
$l['row_m_command'] = "Missing command";

$l['edit_command_success_message'] = "The command has been successfully edited";
$l['command_not_found'] = "Command not found";
$l['delete_question'] = "Are you sure you want to delete the command?";
$l['save'] = "Save";
