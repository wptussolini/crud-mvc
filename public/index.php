<?php
define("BASE_PATH", __DIR__.'/../');

require_once BASE_PATH.'/App/src/helpers.php';

require_once BASE_PATH.'/vendor/autoload.php';

$route = new \App\src\Router;

require_once BASE_PATH.'/App/routes.php';

$route->set404(
    function () {
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
    }
);

$route->run();