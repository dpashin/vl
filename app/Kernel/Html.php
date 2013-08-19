<?php

namespace Kernel;

class Html
{

    public static function encode($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

}