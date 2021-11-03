<?php
    namespace Controller;

    class Router
    {
        private $routes = array();
        private $noPathFunc;
        private $noMethodFunc;

        public function __construct()
        {}

        public function addRoute($route) 
        {
            array_push($this->routes,$route);
        }

        public function resolveRoute() {
            $target = formatRoute($_REQUEST['q']);
            $method = $_SERVER['REQUEST_METHOD'];
            $maxAnswer = 0;
            //check for all routes created, if matches with the target
            foreach ($this->routes as &$value) {
                $matches = $this->isRequestMatch($value,$target,$method);
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
                if (isset($this->noPathFunc)) {
                    ($this->noPathFunc)();
                }
            } else if ($maxAnswer==2) {
                if (isset($this->noMethodFunc)) {
                    ($this->noMethodFunc)();
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
        public function isRequestMatch($route,$routeStr,$method) {
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

        public function setPathNotFound($func) {
            $this->noPathFunc = $func;
        }

        public function setMethodNotFound($func) {
            $this->noMethodFunc = $func;
        }
    }
    