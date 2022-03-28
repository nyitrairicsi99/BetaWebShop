<?php
  namespace Controller;
  
  use PDO;

  class StatisticsController {
    private static $instance = null;
    private static $pdo = null;
    
    private function __construct()
    {
    }

    public static function getInstance() {
      if (self::$instance == null)
      {
        self::$instance = new StatisticsController();
        
        DatabaseConnection::getInstance();
        self::$pdo = DatabaseConnection::$connection;
      }
      
      return self::$instance;
    }

    public static function saveVisitor() {
      $ipdetails = getLocationInfoByIp();
      $ip = $ipdetails['ip'];
      $country = $ipdetails['country'];
      $sql = 'SELECT id FROM visitors WHERE date>DATE_SUB(NOW(),INTERVAL 1 HOUR) AND ip=:ip';
      $statement = self::$pdo->prepare($sql);
      $statement->execute([
        "ip" => $ip
      ]);
      if ($statement->rowCount()==0) {
        $sql = 'INSERT INTO `visitors`(`ip`, `country`) VALUES (:ip,:country)';
        $statement = self::$pdo->prepare($sql);
        $statement->execute([
          "ip" => $ip,
          "country" => $country,
        ]);
      }
    }

  }