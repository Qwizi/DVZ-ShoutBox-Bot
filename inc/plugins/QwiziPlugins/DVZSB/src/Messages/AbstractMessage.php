<?php

namespace Qwizi\DVZSB\Messages;

use MyBB;

class AbstractMessage
{
    protected $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function createLink(string $bburl, string $url, string $title): string
    {
        $title = htmlspecialchars_uni($title);
        return "[url=" . $bburl . "/" . $url . "]" . $title . "[/url]";
    }

    public function convert($data)
    {
        $message = $this->message;
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                $message = str_replace('{' . $key . '}', $value, $message);
            }
        }

        $this->setMessage($message);

        return $message;
    }
}

