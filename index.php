<?php
    $loadFolders = array('utility','MVC/*');
    foreach ($loadFolders as $folder) {
        foreach (glob( __DIR__ . '/'.$folder.'/*.php') as $file) {
            require($file);   
        } 
    }
    global $settings;

    if ($GLOBALS['settings']['showErrors']) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    } else {
        error_reporting(0);
        ini_set('display_errors', 0);
    }

    $neededExtensions = array('pdo_mysql');
    foreach($neededExtensions as $extension) {
        if (!extension_loaded($extension)) {
            if ($GLOBALS['settings']['showErrors']) {
                die($extension." not enabled. <br />");
            }
            http_response_code(500);
            exit;
        }
    }

    use Controller\DatabaseConnection;
    use Controller\Router;
    use Model\Route;

    
    
    //Database connection for requests
    $pdo = new DatabaseConnection();
    $pdo = $pdo->connection;
    


    //Hardcoded routes
    $router = new Router();

    $router->addRoute(new Route("aa/[0-9]",function(){
        echo "found 0-9";
    },"GET"));

    $router->addRoute(new Route("",function(){
        echo "home";
    },"GET"));

    $router->addRoute(new Route("admin",function(){
        echo "admin";
    },"GET"));

    $router->setPathNotFound(function(){
        echo "no path";
    });

    $router->setMethodNotFound(function(){
        echo "no method";
    });

    $router->resolveRoute();


?>