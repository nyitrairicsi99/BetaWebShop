<?php
    namespace Controller;

    class Router
    {
        private static $routes = array();
        private static $noPathFunc;
        private static $noMethodFunc;
        private static $instance = null;

        public function __construct()
        {
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new Router();
            }

            return self::$instance;
        }

        public static function addRoute($route) 
        {
            array_push(self::$routes,$route);
        }

        public static function resolveRoute() {
            $target = formatRoute($_REQUEST['q']);
            $method = $_SERVER['REQUEST_METHOD'];
            $maxAnswer = 0;
            //check for all routes created, if matches with the target
            foreach (self::$routes as &$value) {
                $matches = self::isRequestMatch($value,$target,$method);
                if ($matches==3) {
                    $routeArr = explode("/",$target);
                    array_splice($routeArr,0,1);
                    $value->resolve($routeArr);
                    return;
                }
                if ($matches>$maxAnswer) {
                    $maxAnswer = $matches;
                }
            }
            if ($maxAnswer<=1) {
                if (isset(self::$noPathFunc)) {
                    (self::$noPathFunc)();
                }
            } else if ($maxAnswer==2) {
                if (isset(self::$noMethodFunc)) {
                    (self::$noMethodFunc)();
                }
            }
        }

        /**
         * isRequestMatch
         *
         * @param  Route $route 
         * @param  string $routeStr
         * @param  string $method
         * @return number 
         * 1 = Path not exists.
         * 2 = Path exists but method not.
         * 3 = Path and method correct.
         */
        public static function isRequestMatch($route,$routeStr,$method) {
            $routeCheck = explode("/",$routeStr);
            if (sizeof($routeCheck)==sizeof($route->route)){
                $i = 0;
                foreach ($routeCheck as &$value) {
                    if ($i>0) {
                        $str = $value;
                        $pattern = "/^".$route->route[$i]."+$/";
                        if ($route->route[$i]=="") {
                            if ($str!="") {
                                return 1;
                            }
                        } else if (preg_match($pattern, $str)==0) {
                            return 1;
                        }
                    }
                    $i += 1;
                }
                if($method==$route->method){
                    return 3;
                } else {
                    return 2;
                }
            }
            return 1;
        }

        public static function setPathNotFound($func) {
            self::$noPathFunc = $func;
        }

        public static function setMethodNotFound($func) {
            self::$noMethodFunc = $func;
        }
    }
    