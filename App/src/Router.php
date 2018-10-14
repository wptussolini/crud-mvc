<?php

namespace App\src;

class Router
{
    /**
     * @var array
     */
    protected $afterRoutes = [];

    /**
     * @var string
     */
    private $requestedMethod = '';

    /**
     * @var
     */
    private $serverBasePath;

    /**
     * @var string
     */
    private $namespace = '';

    /**
     * @var
     */
    protected $notFoundCallback;

    /**
     * @param $path
     * @param $fn
     */
    public function get($path, $fn)
    {
        $this->match('GET', $path, $fn);
    }

    /**
     * @param $path
     * @param $fn
     */
    public function post($path, $fn)
    {
        $this->match('POST', $path, $fn);
    }

    /**
     * @param $methods
     * @param $path
     * @param $fn
     */
    public function match($methods, $path, $fn)
    {
        foreach (explode('|', $methods) as $method) {
            $this->afterRoutes[$method][] = array(
                'path' => $path,
                'fn' => $fn,
            );
        }
    }

    /**
     * @param  array $callback
     * @return bool
     */
    public function run($callback = [])
    {
        $this->requestedMethod = $_SERVER['REQUEST_METHOD'];

        $numHandled = 0;

        if (isset($this->afterRoutes[$this->requestedMethod])) {
            $numHandled = $this->handle($this->afterRoutes[$this->requestedMethod], true);
        }

        if ($numHandled === 0) {
            if ($this->notFoundCallback) {
                $this->invoke($this->notFoundCallback);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
        } else {
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }
        return $numHandled !== 0;
    }

    /**
     * @param $fn
     */
    public function set404($fn)
    {
        $this->notFoundCallback = $fn;
    }

    /**
     * @param $namespace
     */
    public function setNamespace($namespace)
    {
        if (is_string($namespace)) {
            $this->namespace = $namespace;
        }
    }

    /**
     * @param  $routes
     * @param  bool   $quitAfterRun
     * @return int
     */
    private function handle($routes, $quitAfterRun = false)
    {
        $numHandled = 0;

        $uri = $this->getCurrentUri();

        foreach ($routes as $route) {
            $route['path'] = preg_replace('/{([A-Za-z]*?)}/', '(\w+)', $route['path']);

            if (preg_match_all('#^' . $route['path'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                $matches = array_slice($matches, 1);
                $params = array_map(
                    function ($match, $index) use ($matches) {

                        if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                            return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                        } else {
                            return isset($match[0][0]) ? trim($match[0][0], '/') : null;
                        }

                    }, $matches, array_keys($matches)
                );
                $this->invoke($route['fn'], $params);
                ++$numHandled;
                if ($quitAfterRun) {
                    break;
                }
            }
        }

        return $numHandled;
    }

    /**
     * @param $fn
     * @param array $params
     */
    private function invoke($fn, $params = array())
    {
        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        } elseif (stripos($fn, '@') !== false) {
            list($controller, $method) = explode('@', $fn);
            try {
                if ($this->getNamespace() !== '') {
                    $controller = $this->getNamespace() . '\\' . $controller;
                } else {
                    throw new \Exception('Controller Namespace not defined', 404);
                }
            } catch (\Exception $e) {
                echo $e->getCode() . ' - ' . $e->getMessage();
            }

            try {
                if (class_exists($controller)) {
                        $c = call_user_func_array(array(new $controller(), $method), $params);
                    if ($c === false) {
                    }
                        echo $c;
                } else {
                    throw new \Exception('Controller not found.', 404);
                }
            } catch (\Exception $e) {
                echo $e->getCode() . ' - ' . $e->getMessage();
            }

        }
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    protected function getCurrentUri()
    {
        $uri = substr($_SERVER['REQUEST_URI'], strlen($this->getBasePath()));
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        return '/' . trim($uri, '/');
    }


    /**
     * @return string
     */
    protected function getBasePath()
    {
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }
        return $this->serverBasePath;
    }
}