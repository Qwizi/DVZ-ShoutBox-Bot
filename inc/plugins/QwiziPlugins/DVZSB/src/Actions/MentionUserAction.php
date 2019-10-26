<?php

declare(strict_types=1);

namespace Qwizi\DVZSB\Actions;

use Qwizi\DVZSB\Actions\ActionInterface;

class MentionUserAction implements ActionInterface
{
    /**
     * Mention username
     * 
     * @param string $username Username
     * 
     * @return string
     */
    public function execute($target, $additonal = null)
    {
        return "@\"".$username."\"";
    }
}