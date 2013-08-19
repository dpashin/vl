<?php

namespace Kernel;

class AutoLoader
{
    private $paths = [];

    public function addPath($path)
    {
        $this->paths[] = realpath($path);
    }

    public function process($class)
    {
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . ".php";
        foreach ($this->paths as $path) {
            $pathFile = $path . DIRECTORY_SEPARATOR . $file;
            if (file_exists($pathFile)) {
                require_once $pathFile;
                return true;
            }
        }
        return false;
    }

    public function register()
    {
        spl_autoload_register([$this, 'process']);
    }

}