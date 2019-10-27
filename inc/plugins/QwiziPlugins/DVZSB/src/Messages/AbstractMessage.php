<?php

namespace Qwizi\DVZSB\Messages;

use MyBB;

class AbstractMessage
{
    /** @var string */
    protected $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Get the value of message
     */ 
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     */ 
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Create bburl link
     * 
     * @param string $bburl
     * @param string $url
     * @param string $title
     * 
     * @return string
     */
    public function createLink(string $bburl, string $url, string $title): string
    {
        $title = htmlspecialchars_uni($title);
        return "[url=" . $bburl . "/" . $url . "]" . $title . "[/url]";
    }

    /**
     * Convert data message
     * 
     * @param array $data
     * 
     * @return string
     */
    public function convert(array $data)
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

