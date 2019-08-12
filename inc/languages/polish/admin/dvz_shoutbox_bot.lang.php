<?php
//! BOT ID
$l['id_t'] = 'ID bota';
$l['id_d'] = 'Podaj id użytkownika, który będzie wysyłał wiadomość na czacie.';

//! REGISTER ACTION SETTINGS
$l['register_t'] = 'Czy bot ma wysyłać wiadomość na czacie, gdy użytkownik dokona rejestracji?';
$l['register_d'] = 'Określa czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik dokona rejestracji.';
$l['register_message_t'] = 'Wiadomość wysyłana na czacie, jeżeli użytkownik dokona rejestracji';
$l['register_message_d'] = 'Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b> aby zastąpić login.';
$l['register_message_example'] = 'Właśnie zarejestrował się {username}. Serdecznie witamy!';

//! THREAD ACTION SETTINGS
$l['thread_t'] = 'Czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy wątek?';
$l['thread_d'] = 'Określa, czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy wątek.';
$l['thread_message_t'] = 'Wiadomość wysyłana na czacie, jeżeli użytkownik napisze nowy wątek';
$l['thread_message_d'] = 'Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b>, aby zastąpić login, <b>{subject}</b>, aby pobrać tytuł wątku, <b>{forum}</b>, aby pobrać nazwe działu.';
$l['thread_message_example'] = 'Nowy wątek - {subject} w dziale {forum}. [quote="{username}" pid="{pid}" dateline="{dateline}"]{message}[/quote]';

//! POST ACTION SETTINGS
$l['post_t'] = 'Czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy post?';
$l['post_d'] = 'Określa, czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy post.';
$l['post_message_t'] = 'Wiadomość wysyłana na czacie, jeżeli użytkownik napisze nowy post';
$l['post_message_d'] = 'Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b>, aby zastąpić login. A <b>{subject}</b>, aby pobrać tytuł postu.';
$l['post_message_example'] = 'Nowy post - {subject}. [quote="{username}" pid="{pid}" dateline="{dateline}"]{message}[/quote]';

//! INGORE FORUMS
$l['ignore_t'] = "Ignorowane fora";
$l['ignore_d'] = "Określa ignorowane fora";

//! COMMANDS
$l['commands_t'] = "Czy bot ma reagować na komendy?";
$l['commands_d'] = "Określa, czy bot ma reagować na komendy";
$l['commands_prefix_t'] = "Prefix dla komend";
$l['commands_prefix_d'] = "Określa prefix dla komend";

$l['manage_commands_t'] = "Zarządzaj komendami";
$l['manage_commands_d'] = "Zarzadzaj komendami";
$l['reload_commands_t'] = "Przeładuj komendy";
$l['reload_commands_d'] = "Przeładuj komendy";
$l['edit_command_t'] = "Edytuj komende";
$l['edit_command_d'] = "Edytuj komende";

$l['row_name_t'] = "Nazwa";
$l['row_name_d'] = "Nazwa komendy";
$l['row_description_t'] = "Opis";
$l['row_description_d'] = "Opis komendy";
$l['row_command_t'] = "Komenda";
$l['row_command_d'] = "Komenda, która będzie wpisywana na czacie";
$l['row_activated_t'] = "Aktywna";
$l['row_activated_d'] = "Czy komenda ma być aktywna";
$l['row_activated_y'] = "tak";
$l['row_activated_n'] = "nie";
$l['row_options'] = "Opcje";
$l['row_options_e'] = "Edytuj";
$l['row_options_d'] = "Usuń";
$l['row_empty'] = "Brak komend";

$l['row_m_name'] = 'Nieprawidłowa nazwa';
$l['row_m_description'] = "Nieprawidłowy opis";
$l['row_m_command'] = "Nieprawidłowa komenda";

$l['edit_command_success_message'] = "Pomyślnie wyedytowano komende";
$l['command_not_found'] = "Nie znaleziono komendy";
$l['delete_question'] = "Napewno chcesz usunąc komende?";
$l['save'] = "Zapisz";