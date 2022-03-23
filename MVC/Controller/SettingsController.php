<?php
    namespace Controller;

    use PDO;

    class SettingsController {
        private static $instance = null;
        public static $theme = "default";
        public static $shopname = "default";
        public static $language = 1;
        public static $smtphost = null;
        public static $smtpuser = null;
        public static $smtppass = null;

        private function __construct()
        {
            
            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;

            $sql = "
              SELECT
                themes.folder as theme, 
                settings.languages_id as languages_id, 
                settings.webshop_name as webshop_name,
                settings.smtp_host as smtp_host,
                settings.smtp_user as smtp_user,
                settings.smtp_pass as smtp_pass
              FROM
                settings,
                themes
              WHERE
                themes.id=settings.themes_id
            ";
            $statement = $pdo->prepare($sql);
            $statement->execute();

            $settings = $statement->fetch(PDO::FETCH_ASSOC);
            if ($settings) {
                self::$shopname = $settings['webshop_name'];
                self::$theme = $settings['theme'];
                self::$language = $settings['languages_id'];
                self::$smtphost = $settings['smtp_host'];
                self::$smtpuser = $settings['smtp_user'];
                self::$smtppass = $settings['smtp_pass'];
            }
        }

        public static function getInstance() {
          if (self::$instance == null)
          {
            self::$instance = new SettingsController();
          }
      
          return self::$instance;
        }

    }