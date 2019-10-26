<?php

namespace Qwizi\DVZSB\Validators;

interface ValidatorInterface
{
    public function validate($target, $additional);
}