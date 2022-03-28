<?php
    namespace Controller;

    class SearchController {

        private static $instance = null;
        private static $searchStr = "";

        private function __construct()
        {
            if (isset($_GET['search'])) {
                self::$searchStr = $_GET['search'];
            }
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new SearchController();
            }

            return self::$instance;
        }

        public static function searchProduct() {
            $searchStr = $_POST['search'];
            $category = $_POST['category'];
            redirect($category,[
                "search" => $searchStr
            ]);
        }

        public static function getSearchValue() {
            return self::$searchStr;
        }
    }