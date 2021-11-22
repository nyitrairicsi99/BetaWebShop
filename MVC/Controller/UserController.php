<?php
    namespace Controller;

    use Model\User;
    
    class UserController {
        private static $instance = null;
        public static $loggedUser = null;
        public static $islogged = null;
        private function __construct()
        {
            if (isset($_SESSION["loggedUser"])) {
                self::$loggedUser = unserialize($_SESSION["loggedUser"]);
                self::$islogged = true;
            } else {
                self::$islogged = false;
            }
        }

        public static function getInstance() {
          if (self::$instance == null)
          {
            self::$instance = new UserController();
          }
      
          return self::$instance;
        }

        public static function logout() {
            self::$loggedUser = null;
            self::$islogged = false;
            session_unset();
            redirect("main");
        }

        public static function login() {
            $rememberme = isset($_POST['rememberme']);
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (self::$loggedUser!=null) {
                self::logout();
            }

            //belépés

            self::$loggedUser = new User($username);
            self::$islogged = true;

            //adatfeltöltés

            $_SESSION["loggedUser"] = serialize(self::$loggedUser);

            redirect("main");
        }

        
        public static function register() {

            //regisztráció

            redirect("main");
        }
    }