<?php
    session_start();

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

    use Controller\StoreController;
    use Controller\AdminController;
    use Controller\UserController;
    use Controller\Router;
    use Controller\ProductController;
    use Model\Route;



    //Hardcoded routes
    $router = new Router();

    $router->addRoute(new Route("",function($routeVarArr){
        redirect("main/1",$_GET);
    },"GET"));

    $router->addRoute(new Route("main",function($routeVarArr){
        redirect("main/1",$_GET);
    },"GET"));

    $router->addRoute(new Route("main/[0-9]",function($routeVarArr){
        new StoreController($routeVarArr[0]);
    },"GET"));

    
    $router->addRoute(new Route("product/[0-9]",function($routeVarArr){
        new ProductController($routeVarArr[0]);
    },"GET"));

    $router->addRoute(new Route("admin",function($routeVarArr){
        new AdminController('statistics',1);
    },"GET"));

    $router->addRoute(new Route("admin/[a-z]",function($routeVarArr){
        new AdminController($routeVarArr[1],1);
    },"GET"));

    $router->addRoute(new Route("admin/[a-z]/[0-9]",function($routeVarArr){
        new AdminController($routeVarArr[1],intval($routeVarArr[2]));
    },"GET"));

    $router->addRoute(new Route("logout",function($routeVarArr){
        UserController::getInstance();
        UserController::logout();
    },"GET"));

    $router->addRoute(new Route("login",function($routeVarArr){
        UserController::getInstance();
        UserController::login();
    },"POST"));

    $router->addRoute(new Route("register",function($routeVarArr){
        UserController::getInstance();
        UserController::register();
    },"POST"));

    $router->setPathNotFound(function(){
        echo "no path";
    });

    $router->setMethodNotFound(function(){
        http_response_code(405);
        exit;
    });

    $router->resolveRoute();

?>