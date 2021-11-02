<?php
    $loadFolders = array('utility','MVC/*');
    

    foreach ($loadFolders as $folder) {
        foreach (glob( __DIR__ . '/'.$folder.'/*.php') as $file) {
            require($file);   
        } 
    }

    if ($settings['showErrors']) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    } else {
        error_reporting(0);
        ini_set('display_errors', 0);
    }

    $neededExtensions = array('php_pdo_mysql');
    foreach($neededExtensions as $extension) {
        if (!extension_loaded($extension)) {
            if ($settings['showErrors']) {
                die($extension." not enabled. <br />");
            }
            http_response_code(500);
        }
    }

    use Controller\Router;
    use Model\Route;
    
    $router = new Router();

    $router->addRoute(new Route("aa/[0-9]",function(){
        echo "found 0-9";
    },"GET"));

    $router->addRoute(new Route("",function(){
        echo "home";
    },"GET"));

    $router->setPathNotFound(function(){
        echo "no path";
    });

    $router->setMethodNotFound(function(){
        echo "no method";
    });

    $router->resolveRoute();
?>