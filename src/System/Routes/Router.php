<?php

namespace Src\System\Routes;

use Src\System\Response\Response;

class Router
{
    public static function get(string $path, callable $callback)
    {
        global $routes;

        $routes[$path]['callback'] = $callback;
        $routes[$path]['method'] = 'GET';
    }

    public static function post(string $path, callable $callback)
    {
        global $routes;

        $routes[$path]['callback'] = $callback;
        $routes[$path]['method'] = 'POST';
    }

    public static function run()
    {
        global $routes;

        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        
        $found = false;
        $allowedMethod = false;

        foreach ($routes as $path => $item) {
            if ($path !== $uri) continue;
            $found = true;

            if ($_SERVER['REQUEST_METHOD'] !== $item['method']) continue;
            $allowedMethod = true;

            $item['callback']();
        }

        if (!$found) {
            (new Response())->notFound();
        }

        if (!$allowedMethod) {
            (new Response())->methodNotAllowed();
        }
    }
}
