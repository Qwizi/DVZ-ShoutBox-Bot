<?php
$l['bot_title'] = 'DVZ ShoutBox Bot';
$l['bot_desc'] = 'Bot wysyłający wiadomość na czacie, jeżeli użytkownik dokona rejestracji lub napisze nowy wątek/post. Odpowiada na wiadomości podane przez admina.';
$l['bot_setting_desc'] = 'Ustawienia pluginu DVZ ShoutBox Bot.';
$l['bot_onoff_title'] = 'Plugin włączony/wyłączony';
$l['bot_onoff_desc'] = 'Plugin ma być włączony/wyłączony?';
$l['bot_link_title'] = 'Pobierac link do loginu?';
$l['bot_link_desc'] = 'Określa, czy plugin ma pobierać link do profilu. Przykład @Nick.';
$l['bot_action_title'] = 'Czy bot ma reagować na wiadomości wpisane na czacie?';
$l['bot_action_desc'] = 'Określa czy bot ma odpowiadać na wiadomości podane w panelu admina. Można zarządzać nimi <a href="index.php?module=user-dvz-shoutbox-bot">tutaj</a>.';
$l['bot_id_title'] = 'ID bota';
$l['bot_id_desc'] = 'Podaj id użytkownika, który będzie wysyłał wiadomość na czacie.';
$l['bot_register_title'] = 'Czy bot ma wysyłać wiadomość na czacie, gdy użytkownik dokona rejestracji?';
$l['bot_register_desc'] = 'Określa czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik dokona rejestracji.';
$l['bot_register_message_title'] = 'Wiadomość wysyłana na czacie, jeżeli użytkownik dokona rejestracji';
$l['bot_register_message_desc'] = 'Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b> aby zastąpić login.';
$l['bot_register_message_example'] = 'Właśnie zarejestrował się {username}. Serdecznie witamy!';
$l['bot_thread_title'] = 'Czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy wątek?';
$l['bot_thread_desc'] = 'Określa, czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy wątek.';
$l['bot_ignore_title'] = 'Ignorowanie for';
$l['bot_ignore_desc'] = 'Wybierz z listy fora, którę mają być ignorowane przez bota.';
$l['bot_thread_message_title'] = 'Wiadomość wysyłana na czacie, jeżeli użytkownik napisze nowy wątek';
$l['bot_thread_message_desc'] = 'Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b>, aby zastąpić login, <b>{subject}</b>, aby pobrać tytuł wątku, <b>{forum}</b>, aby pobrać nazwe działu.';
$l['bot_thread_message_example'] = 'Nowy wątek - {subject} w dziale {forum}. Napisany przez {username}';
$l['bot_post_title'] = 'Czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy post?';
$l['bot_post_desc'] = 'Określa, czy bot ma wysyłać wiadomość na czacie, jeżeli użytkownik napisze nowy post.';
$l['bot_post_message_title'] = 'Wiadomość wysyłana na czacie, jeżeli użytkownik napisze nowy post';
$l['bot_post_message_desc'] = 'Wiadomość, która zostanie wysłana przez bota. Użyj <b>{username}</b>, aby zastąpić login. A <b>{subject}</b>, aby pobrać tytuł postu.';
$l['bot_post_message_example'] = 'Nowy post - {subject}. Napisany przez {username}';
//TODO: dodać langi poniższe do /english
$l['bot_commands_onoff_title'] = 'Komendy włączone/wyłączone';
$l['bot_commands_onoff_desc'] = 'Określa, czy komendy mają być włączone';
$l['bot_commands_prefix_title'] = 'Prefix do komend';
$l['bot_commands_prefix_desc'] = 'Określa prefix do komend';
$l['bot_commandsData_ban_name'] = 'Ban';
$l['bot_commandsData_ban_desc'] = 'Komenda ta pozwala banować użytkowników';
$l['bot_commandsData_unBan_name'] = 'UnBan';
$l['bot_commandsData_unBan_desc'] = 'Komenda ta pozwala zdejmować blokady użytkowników';
$l['bot_commandsData_banList_name'] = 'Lista banów';
$l['bot_commandsData_banList_desc'] = 'Komenda ta pokazuje aktualnie kto jest zbanowany';
$l['bot_commandsData_prune_name'] = 'Prune';
$l['bot_commandsData_prune_desc'] = 'Komenda ta pozwala na usuwanie wpisów';
$l['bot_commandsData_setBot_name'] = 'SetBot';
$l['bot_commandsData_setBot_desc'] = 'Komenda ta pozwala na ustawienie konta bota';
$l['bot_commandsData_help_name'] = 'Help';
$l['bot_commandsData_help_desc'] = 'Lista komend';
$l['bot_commandsData_steamID64_desc'] = 'Konwertuje steamid32 do steamid64';
$l['bot_commandsData_steamID32_desc'] = 'Konwertuje steamid64 do steamid32';
//SetBot.php
$l['bot_setbot_error_empty_user'] = "Nie znaleziono użytkownika";
$l['bot_setbot_message_success'] = " zmienił konto bota na ";
//Prune.php
$l['bot_prune_all_message'] = "Czat został wyczyszczony.";
$l['bot_prune_message_user_success'] = " usunął wiadomości użytkownika ";
//Ban.php
$l['bot_ban_error_empty_user'] = "Nie znaleziono użytkownika";
$l['bot_ban_error_multiban_user'] = "Nie możesz ponownie zbanować tego uzytkownika";
$l['bot_ban_error_ban_myself'] = "Nie możesz sam siebie zbanować";
$l['bot_ban_message_success'] = " zbanował użytkownika ";
//BanList.php
$['bot_banlist_empty_list'] = "Brak zbanowanych użytkowników";
$l['bot_banlist_list_banned'] = "Zbanowani: ";
//Help.php
$l['bot_help_error'] = "Wystąpił problem.";
//UnBan.php
$l['bot_unban_empty_user'] = "Nie znaleziono użytkownika.";
$l['bot_unban_no_ban'] = "Użytkownik nie posiada bana.";
$l['bot_unban_error_unban_myself'] = "Nie możesz siebie odbanować.";
$l['bot_unban_message_success'] = " odbanował użytkownika ";
//SteamID32.php
$l['bot_steamid32_error'] = "Wystąpił problem.";
//SteamID64.php
$l['bot_steamid64_error'] = "Wystąpił problem.";

$l['bot_panel_view_title'] = 'Widok';
$l['bot_panel_view_desc'] = 'Zarządzaj wiadomościami';
$l['bot_panel_add_title'] = 'Dodaj';
$l['bot_panel_add_desc'] = "Dodaj nowe akcje";
$l['bot_panel_edit_title'] = 'Edytuj';
$l['bot_panel_edit_desc'] = "Edytuj akcje";
$l['bot_panel_delete_title'] = 'Usuń';
$l['bot_panel_delete_desc'] = "Usuń akcje";
$l['bot_panel_message'] = 'Wiadomość';
$l['bot_panel_message_desc'] = 'Wpisz tutaj wiadomość';
$l['bot_panel_nomessage'] = 'Brak wiadomości';
$l['bot_panel_answer'] = 'Odpowiedź';
$l['bot_panel_answer_desc'] = 'Wpisz tutaj odpowiedź';
$l['bot_panel_options'] = 'Opcje';
$l['bot_panel_empty_fields'] = 'Pola nie mogą być puste';
$l['bot_panel_characters_m'] = 'W wiadomości znajdują sie niedozwolone znaki';
$l['bot_panel_characters_a'] = 'W odpowiedzi znajdują sie niedozwolone znaki';
$l['bot_panel_action_success'] = 'Pomyślnie dodano akcje';
$l['bot_panel_action_edit_success'] = 'Pomyślnie edytowano akcje';
$l['bot_panel_delete'] = 'Czy napewno chcesz usunąc?';
$l['bot_panel_yes'] = 'Tak';
$l['bot_panel_no'] = 'Nie';
