<?php

namespace Kernel;

require_once __DIR__ . '/AutoLoader.php';

class App
{
    protected static $_db;
    protected static $_config;
    protected static $_identity;

    public static function run()
    {
        $autoloader = new AutoLoader();
        $autoloader->addPath(__DIR__ . '/../');
        $autoloader->register();

        session_start();

        // TODO improve routing
        $url = parse_url($_SERVER["REQUEST_URI"]);
        $path = explode('/', $url['path']);
        $action = "action" . (empty($path[1]) ? 'Index' : $path[1]);
        $controller = new \Controller\Main();
        if (method_exists($controller, $action))
            $controller->$action();
        else
            $controller->error(404, 'Страница не найдена');
    }

    public static function db()
    {
        if (!isset(self::$_db)) {
            self::$_db = new \PDO(self::config()->db->dsn, self::config()->db->user, self::config()->db->password);
            self::$_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return self::$_db;
    }

    public static function config()
    {
        if (!isset(self::$_config))
            self::$_config = json_decode(file_get_contents(__DIR__ . '/../../etc/app.json'));
        return self::$_config;
    }

    public static function identity()
    {
        if (!isset(self::$_identity))
            self::$_identity = new Identity();
        return self::$_identity;
    }

    public static function redirect($url)
    {
        header('Location: ' . $url, true, 302);
        exit(0);
    }

    public static function setFlash($key, $value)
    {
        $_SESSION["flash_$key"] = $value;
    }

    public static function getFlash($key)
    {
        if (array_key_exists("flash_$key", $_SESSION)) {
            $value = $_SESSION["flash_$key"];
            unset($_SESSION["flash_$key"]);
            return $value;
        }
        return null;
    }


}
