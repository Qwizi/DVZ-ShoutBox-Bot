<?php

namespace Qwizi\DVZSB\Interfaces;

interface ValidationInterface
{
    public function validate($target, array $additional);
}