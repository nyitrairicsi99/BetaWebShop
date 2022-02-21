<?php
    namespace Controller;
    
    use PDO;

    class AddonController {
        private static $instance = null;
        public static $enabledAddons = [];
        public static $addons = [];
        private function __construct()
        {
        }

        public static function getInstance() {
          if (self::$instance == null)
          {
            self::$instance = new AddonController();

            
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            $sql = 'SELECT id,name,enabled FROM installed_plugins';
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $addons = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach($addons as $addon) {
              array_push(self::$addons,[
                "id" => $addon['id'],
                "name" => $addon['name'],
                "enabled" => $addon['enabled']==1,
              ]);
              if ($addon['enabled']==1) {
                array_push(self::$enabledAddons,$addon['name']);
              }
            }
          }
      
          return self::$instance;
        }

        public static function runAddons() {
          foreach(self::$enabledAddons as $plugin) {
            if (is_dir($_SERVER['DOCUMENT_ROOT'].$GLOBALS['settings']['root_folder'].'/plugins/'.$plugin)) {
                include $_SERVER['DOCUMENT_ROOT'].$GLOBALS['settings']['root_folder'].'/plugins/'.$plugin.'/index.php';
            }
          }
        }

        public static function getHeaderTags() {
          $str = "";
          foreach(self::$enabledAddons as $plugin) {
            if (is_dir($_SERVER['DOCUMENT_ROOT'].$GLOBALS['settings']['root_folder'].'/plugins/'.$plugin)) {
              $str .= "<script src='".$GLOBALS['settings']['root_folder'].'/plugins/'.$plugin.'/index.js'."'></script>";
            }
          }
          return $str;
        }

        public static function getAddons() {
          return self::$addons;
        }

    }