<?php
    namespace Controller;

    class AddonController {
        private static $instance = null;
        public static $enabledAddons = ['example'];
        private function __construct()
        {
        }

        public static function getInstance() {
          if (self::$instance == null)
          {
            self::$instance = new AddonController();
          }
      
          return self::$instance;
        }

        public static function runAddons() {
          foreach(self::$enabledAddons as $plugin) {
            if (is_dir($_SERVER['DOCUMENT_ROOT'].$GLOBALS['settings']['root_folder'].'/addons/'.$plugin)) {
                include $_SERVER['DOCUMENT_ROOT'].$GLOBALS['settings']['root_folder'].'/addons/'.$plugin.'/index.php';
            }
          }
        }

        public static function getHeaderTags() {
          $str = "";
          foreach(self::$enabledAddons as $plugin) {
            if (is_dir($_SERVER['DOCUMENT_ROOT'].$GLOBALS['settings']['root_folder'].'/addons/'.$plugin)) {
              $str .= "<script src='".$GLOBALS['settings']['root_folder'].'/addons/'.$plugin.'/index.js'."'></script>";
            }
          }
          return $str;
        }

    }