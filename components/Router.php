<?php

class Router
{
    private $routes;

    function __construct()
    {
        $routesPatch = ROOT . "/config/routes.php";
        $this->routes = include($routesPatch);
    }

    /**
     * @return string
     */
    private function getURI()
    {
        if ("/" == $_SERVER['REQUEST_URI'])
            return "auth";
        else
            if (!empty($_SERVER['REQUEST_URI']))
                return trim($_SERVER['REQUEST_URI'], '/');

        return "auth";

    }

    function run()
    {
        $uri = $this->getURI();
        foreach ($this->routes as $uriPattern => $path)
            if (preg_match("~$uriPattern~", $uri)) {
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);
                $segments = explode('/', $internalRoute);


                $controllerName = array_shift($segments) . 'Controller';
                $controllerName = ucfirst($controllerName);


                $actionName = 'action' . ucfirst(array_shift($segments));
                $controllerFile = ROOT . "/controller/" . $controllerName . ".php";
                if (file_exists($controllerFile))
                    require_once($controllerFile);

                $controllerObject = new $controllerName;
                $result = $controllerObject->$actionName($segments);


                if ($result != null)
                    break;
            }

    }

}