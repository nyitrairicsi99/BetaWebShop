<?php
    namespace Controller;
    use PDO;
    use PDOException;

    class DatabaseConnection {
        private static $instance = null;
        public static $connection;
        private function __construct()
        {
            $host = $GLOBALS['settings']['db_host'];
            $db = $GLOBALS['settings']['db_dbname'];
            $user = $GLOBALS['settings']['db_username'];
            $password = $GLOBALS['settings']['db_password'];
            $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";
            try {
                self::$connection = new PDO($dsn, $user, $password);
            } catch (PDOException $e) {
                if ($GLOBALS['settings']['showErrors']) {
                    die($e->getMessage());
                }
                http_response_code(500);
                exit;
            }
        }

        public static function getInstance() {
          if (self::$instance == null)
          {
            self::$instance = new DatabaseConnection();
          }
      
          return self::$instance;
        }
    }
    