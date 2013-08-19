<?php

namespace Kernel;

class Identity
{
    private $sessionVar = 'identity';

    public function get()
    {
        return array_key_exists($this->sessionVar, $_SESSION) ? $_SESSION[$this->sessionVar] : null;
    }

    public function set($value)
    {
        if (is_null($value))
            unset($_SESSION[$this->sessionVar]);
        else
            $_SESSION[$this->sessionVar] = $value;
    }

    public function isEmpty()
    {
        $value = $this->get();
        return empty($value);
    }

}