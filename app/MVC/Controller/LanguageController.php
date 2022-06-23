<?php
    namespace Controller;
    use PDO;

    use Controller\SettingsController;

    class LanguageController {
      private static $instance = null;
      private static $phrases = [];
      public static $longname;
      public static $shortname;

      private function __construct()
      {}

      public static function getInstance() {
        if (self::$instance == null)
        {
          self::$instance = new LanguageController();
        }
        SettingsController::getInstance();
        $language = SettingsController::$language;
        self::setLanguage($language);
      
        return self::$instance;
      }

      public static function setLanguage($language) {
        if ($language!=null) {
          DatabaseConnection::getInstance();
          $pdo = DatabaseConnection::$connection;
          
          $sql = 'SELECT phrase FROM phrases GROUP BY phrase';
          $statement = $pdo->prepare($sql);
          $statement->execute();
          $phrases = $statement->fetchAll(PDO::FETCH_ASSOC);
          if ($phrases) {
            foreach($phrases as $phrase) {
              self::$phrases[$phrase['phrase']] = "";
            }
          }

          $sql = "SELECT phrases.`id` as id, phrases.`phrase` as phrase, phrases.`translated` as translated, languages.`longname` as longname, languages.`shortname` as shortname FROM `phrases`,`languages` WHERE phrases.languages_id=languages.id AND phrases.languages_id=:language";
          $statement = $pdo->prepare($sql);
          $statement->execute([
            ':language' => $language
          ]);
          $phrases = $statement->fetchAll(PDO::FETCH_ASSOC);
          if ($phrases) {
            foreach($phrases as $phrase) {
              self::$phrases[$phrase['phrase']] = $phrase['translated'];
            }
            self::$longname = $phrase['longname'];
            self::$shortname = $phrase['shortname'];
          }
          return true;
        } else {
          return false;
        }
      }

      public static function translate($phrase) {
        if (!array_key_exists($phrase,self::$phrases)) {
          return '(*' . $phrase . '*)';
        } else {
          return (self::$phrases[$phrase]!="") ? self::$phrases[$phrase] : ('(*' . $phrase . '*)');
        }
      }

      public static function getString() {
        return json_encode([
          "longname" => self::$longname,
          "shortname" => self::$shortname,
          "phrases" => self::$phrases,
        ]);
      }

      public static function createLanguage($json) {
        DatabaseConnection::getInstance();
        $pdo = DatabaseConnection::$connection;
        $details = json_decode($json,true);

        if ($details) {
          if (isset($details['longname']) && isset($details['shortname']) && isset($details['phrases'])) {
            $longname = $details['longname'];
            $shortname = $details['shortname'];

            $sql = "SELECT id FROM languages WHERE shortname=:shortname OR longname=:longname";
            $statement = $pdo->prepare($sql);
            $statement->execute([
              ':shortname' => $shortname,
              ':longname' => $longname,
            ]);
            if (!($statement->fetch(PDO::FETCH_ASSOC))) {
              $phrases = $details['phrases'];
              $sql = "INSERT INTO `languages`(`shortname`, `longname`) VALUES (:shortname,:longname)";
              $statement = $pdo->prepare($sql);
              $statement->execute([
                "shortname" => $shortname,
                "longname" => $longname,
              ]);
              $id = $pdo->lastInsertId();
              foreach ($phrases as $phrase=>$translated) {
                $sql = "INSERT INTO `phrases`(`languages_id`, `phrase`, `translated`) VALUES (:language,:phrase,:translated)";
                $statement = $pdo->prepare($sql);
                $statement->execute([
                  "language" => $id,
                  "phrase" => $phrase,
                  "translated" => $translated,
                ]);
              }
              return true;
            }
          }
        }
        return false;
      }

    }