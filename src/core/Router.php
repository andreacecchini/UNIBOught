<?php
namespace core;

use core\Request;
use core\Response;
use Exception;

class Router
{
    private $res;
    private $req;
    private $routes;
    private $middlewares;

    public function __construct(Response $res, Request $req)
    {
        $this->res = $res;
        $this->req = $req;
        $this->middlewares = require_once SOURCE_DIR . '/config/middleware.php';
        $this->loadRoutes();
    }

    public function dispatch()
    {
        $url = $this->req->getPath();
        $url === '' ? '/' : $url;
        $routeParams = $this->getRouteParam(parse_url($url, PHP_URL_PATH));
        if (!isset($routeParams['controller'])) {
            throw new Exception('Controler not setted...');
        }
        $controllerClass = 'controllers\\' . $routeParams['controller'];
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller $controllerClass class doesn't exist...");
        }
        $controller = new $controllerClass($this->req, $this->res);
        if (!isset($routeParams['action'])) {
            throw new Exception('Action not setted...');
        }
        $action = $routeParams['action'];
        if (!method_exists($controller, $action)) {
            throw new Exception('Action not present in Controller class...');
        }
        $middlewares = [];
        if (isset($routeParams['middleware'])) {
            $middlewares = $this->getMiddlewaresFromGroups($routeParams['middleware']);
        }
        $this->execute($controller, $action, $middlewares);
    }

    private function getRouteParam($path)
    {
        $match = $this->match($path);
        if (!$match) {
            // Page not found
            header("Location: /404");
            exit;
        }
        [$routePattern, $matches] = $match;
        $route = $this->routes[$routePattern];
        $method = $this->req->getMethod();
        if (!isset($route[$method])) {
            // Method not allowed
            header("Location: /403");
            exit;
        }
        // Aggiungo gli eventuali parametri matchati nell'URL
        // Es. dettaglio-prodotto/{id}
        foreach ($matches as $key => $value) {
            $this->req->setParam($key, $value);
        }
        return $route[$method];
    }

    private function match($path)
    {
        foreach ($this->routes as $route => $methods) {
            if (preg_match($route, $path, $matches)) {
                // Aggiungo i parametri estratti dall'URL
                // Es. dettaglio-prodotto/{id}
                return [$route, $matches];
            }
        }
        return false;
    }

    private function getMiddlewaresFromGroups($middlewareGroups)
    {
        $middlewares = [];
        foreach ($middlewareGroups as $group) {
            if (isset($this->middlewares[$group])) {
                $middlewares = array_merge($middlewares, $this->middlewares[$group]);
            }
        }
        return array_unique($middlewares);
    }

    private function execute($controller, $action, $middlewares)
    {
        $chain = function () use ($controller, $action) {
            $controller->$action();
        };
        foreach (array_reverse($middlewares) as $middlewareClass) {
            $middlewareClass = 'middlewares\\' . $middlewareClass;
            if (!class_exists($middlewareClass)) {
                throw new Exception("Middleware $middlewareClass doesn't exist...");
            }
            $middleware = new $middlewareClass();
            $chain = function () use ($middleware, $chain) {
                $middleware->handle($chain);
            };
        }
        $chain();
    }

    private function loadRoutes()
    {
        $rs = require_once SOURCE_DIR . '/config/routes.php';
        foreach ($rs as $route => $methods) {
            // Converte il percorso in espressione regolare
            $route = preg_replace('/\//', '\\/', $route);
            // Converte variabili, ad esempio {id}
            $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);
            // Aggiungi inizio e fine del testo
            $route = '/^' . $route . '$/i';
            $this->routes[$route] = $methods;
        }
    }

}