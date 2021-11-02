<?php
    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', 1);

    //require 'MVC/Model/Route.php';
    
    $loadFolders = array('utility','MVC/*');
    
    foreach ($loadFolders as $folder) {
        foreach (glob( __DIR__ . '/'.$folder.'/*.php') as $file) {
            require($file);   
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