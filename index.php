<?php
    session_start();
    ini_set('max_file_uploads', '50');
    ini_set('file_uploads', 'On');
    ini_set('post_max_size', '100M');
    ini_set('upload_max_filesize', '100M');
    
    use Controller\Router;
    use Model\Route;

    $loadFolders = array('utility','MVC/*','PHPMailer');
    foreach ($loadFolders as $folder) {
        foreach (glob( __DIR__ . '/'.$folder.'/*.php') as $file) {
            require($file);   
        } 
    }
    global $settings;
    if (isset($GLOBALS['settings'])) {
        if ($GLOBALS['settings']['showErrors']) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
    } else {

        Router::addRoute(new Route("install",function($routeVarArr){
            include __DIR__ . "/setup/index.php";
        },"GET"));

        Router::addRoute(new Route("install",function($routeVarArr){
            include __DIR__ . "/setup/index.php";
        },"POST"));

        
        Router::setPathNotFound(function(){
            $rootdir = str_replace("\\","/",$_SERVER['DOCUMENT_ROOT']);
            $filedir = str_replace("\\","/",dirname(__FILE__));
            $webdir = str_replace($rootdir,"",$filedir);
        
            $shopdir = str_replace("/setup","",$webdir);
            $shopdir = str_replace("/install","",$shopdir);
            
            header('Location: '.$shopdir.'/install');
        });

        Router::setMethodNotFound(function(){
            http_response_code(405);
            exit;
        });

        Router::resolveRoute();

        die();
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

    
    use Controller\SearchController;
    use Controller\BasketController;
    use Controller\UserController;
    use Controller\DatabaseConnection;
    use Controller\CategoryController;
    use Controller\OrderController;
    use Controller\AddonController;
    use Controller\ProductController;
    use Controller\ProfileController;
    use Controller\StoreController;
    use Controller\AdminController;
    use Controller\AdminActionController;
    use Controller\StatisticsController;
    use Controller\SettingsController;
    use Controller\LanguageController;
    use Controller\MailController;
    

    DatabaseConnection::getInstance();
    $pdo = DatabaseConnection::$connection;

    Router::getInstance();

    Router::addRoute(new Route("2fa",function($routeVarArr){
        if (isset($_POST['code'])) {
            $code = $_POST['code'];
    
            UserController::getInstance();
            UserController::insert2fa($code);
        }
    },"POST"));

    Router::addRoute(new Route("basket/[0-9]",function($routeVarArr){
        BasketController::getInstance();
        BasketController::removeItem($routeVarArr[1]);
    },"POST"));

    Router::addRoute(new Route("[a-z]",function($routeVarArr){
        switch ($routeVarArr[0]) {
            case 'login':
                UserController::getInstance();
                UserController::login();
                break;
            case 'register':
                UserController::getInstance();
                UserController::register();
                break;
            case 'profile':
                ProfileController::getInstance();
                ProfileController::modify();
                break;
            case 'basket':
                BasketController::getInstance();
                BasketController::addItem();
                break;
            case 'search':
                SearchController::getInstance();
                SearchController::searchProduct();
                break;
            case 'order':
                OrderController::getInstance();
                OrderController::makeOrder();
                break;
            default:
                $action = $_POST['action'];
                AdminActionController::getInstance();
                AdminActionController::adminAction($routeVarArr[0],$action,"POST");
                break;
        }
    },"POST"));
    
    Router::addRoute(new Route("admin",function($routeVarArr){
        AdminController::getInstance();
        AdminController::createView('statistics',1);
    },"GET"));

    Router::addRoute(new Route("admin/[a-z]",function($routeVarArr){
        AdminController::getInstance();
        AdminController::createView($routeVarArr[1],1);
    },"GET"));

    Router::addRoute(new Route("admin/[a-z]/[0-9]",function($routeVarArr){
        AdminController::getInstance();
        AdminController::createView($routeVarArr[1],intval($routeVarArr[2]));
    },"GET"));

    Router::addRoute(new Route("admin/[a-z]/[0-9]/[0-9]",function($routeVarArr){
        AdminController::getInstance();
        AdminController::createView($routeVarArr[1],intval($routeVarArr[2]),intval($routeVarArr[3]));
    },"GET"));

    CategoryController::getInstance();
    $categories = CategoryController::getCategories(true,false);
    foreach ($categories as $main) {
        if (count($main["subcategories"])==0) {
            $path = $main["short"];
            Router::addRoute(new Route($path,function($routeVarArr){
                StoreController::getInstance();
                StoreController::createView($routeVarArr[0],1);
            },"GET"));
    
            Router::addRoute(new Route($path."/[0-9]",function($routeVarArr){
                StoreController::getInstance();
                StoreController::createView($routeVarArr[0],$routeVarArr[1]);
            },"GET"));
        } else {
            foreach ($main["subcategories"] as $sub) {
                $path = $sub["short"];
                Router::addRoute(new Route($path,function($routeVarArr){
                    StoreController::getInstance();
                    StoreController::createView($routeVarArr[0],1);
                },"GET"));
        
                Router::addRoute(new Route($path."/[0-9]",function($routeVarArr){
                    StoreController::getInstance();
                    StoreController::createView($routeVarArr[0],$routeVarArr[1]);
                },"GET"));
            }
        }
    }

    Router::addRoute(new Route("",function($routeVarArr){
        StoreController::getInstance();
        StoreController::createView("main",1);
    },"GET"));

    Router::addRoute(new Route("main",function($routeVarArr){
        StoreController::getInstance();
        StoreController::createView("main",1);
    },"GET"));

    Router::addRoute(new Route("main/[0-9]",function($routeVarArr){
        StoreController::getInstance();
        StoreController::createView("main",$routeVarArr[1]);
    },"GET"));
    
    Router::addRoute(new Route("product/[0-9]",function($routeVarArr){
        ProductController::getInstance();
        ProductController::createView($routeVarArr[1]);
    },"GET"));
    
    Router::addRoute(new Route("orders",function($routeVarArr){
        OrderController::getInstance();
        OrderController::createListView();
    },"GET"));

    Router::addRoute(new Route("orders/[0-9]",function($routeVarArr){
        OrderController::getInstance();
        OrderController::createOrderView($routeVarArr[1]);
    },"GET"));

    Router::addRoute(new Route("orderdetails",function($routeVarArr){
        OrderController::getInstance();
        OrderController::createView();
    },"GET"));

    Router::addRoute(new Route("profile",function($routeVarArr){
        $profileController = new ProfileController();
        $profileController->show();
    },"GET"));

    Router::addRoute(new Route("basket",function($routeVarArr){
        BasketController::getInstance();
        BasketController::createView();
    },"GET"));
    
    Router::addRoute(new Route("logout",function($routeVarArr){
        UserController::getInstance();
        UserController::logout();
    },"GET"));

    Router::setPathNotFound(function(){
        redirect("");
    });

    Router::setMethodNotFound(function(){
        http_response_code(405);
        exit;
    });

    UserController::getInstance();
    UserController::checklogincookie();
    
    AddonController::getInstance();
    AddonController::runAddons();

    Router::resolveRoute();

    StatisticsController::getInstance();
    StatisticsController::saveVisitor();
?>