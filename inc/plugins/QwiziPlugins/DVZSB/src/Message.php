<?php

declare(strict_types=1);

namespace Qwizi\DVZSB;

class Message
{
    public static function convert(string $message, array $data=[]): string {
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                $message = str_replace('{' . $key . '}', $value, $message);
            }
        }
        return $message;
    }

    public static function createLink(string $url, string $title, bool $currentSite=false): string
    {
        global $mybb;
        $title = htmlspecialchars_uni($title);
        if ($currentSite) return \sprintf("[url=%s/%s]%s[/url]", $mybb->settings['bburl'], $url, $title);
        return \sprintf("[url=%s]%s[/url]", $url, $title);
    }

    public static function mentionUser(string $username, int $uid): string {
        global $cache;
        // Sprawdzamy czy plugin dvz_mentions jest aktywny
        if ((bool)in_array('dvz_mentions', $cache->cache['plugins']['active'])) {
            return \sprintf("@`%s`#%d", $username, $uid);
        }
        return \sprintf("%s", $username);
    }

    public static function clearHTML(string $html) {
        $html = \strip_tags($html);
        return $html;
    }

    public static function addDotsToMessage(string $message, int $length): string {
        if (\my_strlen($message) > $length) {
            $message = \my_substr($message, 0, $length) . '...';
        }
        return $message;
    }
}