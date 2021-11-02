<?php
    namespace Controller;
    use PDO;
    use PDOException;

    class DatabaseConnection
    {
        public $connection;
        public function __construct()
        {
            $host = $GLOBALS['settings']['db_host'];
            $db = $GLOBALS['settings']['db_dbname'];
            $user = $GLOBALS['settings']['db_username'];
            $password = $GLOBALS['settings']['db_password'];
            $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";
            try {
                $this->connection = new PDO($dsn, $user, $password);
            } catch (PDOException $e) {
                if ($GLOBALS['settings']['showErrors']) {
                    die($e->getMessage());
                }
                http_response_code(500);
                exit;
            }
        }
    }
    