<?php
    namespace Controller;
    use PDO;

    class CategoryController {
        private static $instance = null;
        private static $allcategory = [];
        private function __construct()
        {
            $sql = '
            SELECT
                main.id as main_id,
                sub.id as sub_id,
                sub.name as name,
                sub.short as short,
                sub.display_navbar as display_navbar
            FROM 
                categories as main
            RIGHT JOIN
                categories as sub 
            ON
                main.id=sub.parentcategory
            ORDER BY
                sub.parentcategory
            DESC;
            ';

            DatabaseConnection::getInstance();
            $pdo = DatabaseConnection::$connection;
            $statement = $pdo->prepare($sql);
            $statement->execute();

            $categories = $statement->fetchAll(PDO::FETCH_ASSOC);

            if ($categories) {
                $orderedcategories = [];
                self::$allcategory = [];
                foreach ($categories as $category) {
                    $main = $category['main_id'];
                    $sub = $category['sub_id'];
                    $name = $category['name'];
                    $short = $category['short'];
                    $display = $category['display_navbar'];
                    if (!in_array($sub,$orderedcategories) && isset($main)) {
                        if (!isset($orderedcategories[$main])) {
                            $orderedcategories[$main] = [];
                        }
                        array_push($orderedcategories[$main],["name" =>$name,"short"=>$short,"display"=>$display,"id"=>$sub]);
                    } else {
                        if (isset($orderedcategories[$sub])) {
                            array_push(self::$allcategory,["name"=>$name,"short"=>$short,"subcategories"=>$orderedcategories[$sub],"display"=>$display,"id"=>$sub]);
                        } else {
                            array_push(self::$allcategory,["name"=>$name,"short"=>$short,"subcategories"=>[],"display"=>$display,"id"=>$sub]);
                        }
                    }
                }
            }
        }

        public static function getInstance() {
          if (self::$instance == null)
          {
            self::$instance = new CategoryController();
          }
      
          return self::$instance;
        }

        public static function getCategories($displayed=true,$onlymain = false) {
            $ret = [];
            if ($onlymain) {
                foreach (self::$allcategory as $category) {
                    if ((!$displayed && $category["display"]==0) || ($displayed && $category["display"]==1)) {
                        array_push($ret,["name"=>$category["name"],"short"=>$category["short"],"id"=>$category["id"]]);
                    }
                }
            } else {
                foreach (self::$allcategory as $category) {
                    if ((!$displayed && $category["display"]==0) || ($displayed && $category["display"]==1)) {
                        $validsubcategoies = [];
                        foreach ($category["subcategories"] as $subcategory) {
                            if ((!$displayed && $subcategory["display"]==0) || ($displayed && $subcategory["display"]==1)) {
                                array_push($validsubcategoies,["name"=>$subcategory["name"],"short"=>$subcategory["short"],"id"=>$subcategory["id"]]);
                            }
                        }  
                        array_push($ret,["name"=>$category["name"],"short"=>$category["short"],"subcategories"=>$validsubcategoies,"id"=>$category["id"]]);
                    }
                }
            }
            return $ret;
        }
    }
    