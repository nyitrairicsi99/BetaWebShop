<?php
    namespace Model;

    class Route {

        public $method = "GET";
        public $route = array();
        public $func;

        public function __construct($route,$func,$method)
        {
            $this->route = explode("/",formatRoute($route));
            $this->method = $method;
            $this->func = $func;
        }

        public function resolve() {
            ($this->func)();
        }
    }