<?php
    namespace Controller;

    use PDO;
    use Model\Basket;
    use Model\BasketItem;

    class BasketController {

        private static $instance = null;
        public static $basket;

        private function __construct()
        {
            if (isset($_SESSION["basket"])) {
                self::$basket = unserialize($_SESSION["basket"]);
            } else {
                self::$basket = new Basket();
            }
            self::saveBasket();
        }

        public static function getInstance() {
            if (self::$instance == null)
            {
                self::$instance = new BasketController();
            }

            return self::$instance;
        }

        public static function addItem() {
            $id = $_POST['id'];
            $piece = $_POST['piece'];
            self::$basket->addItem(new BasketItem($id,$piece));
            self::saveBasket();
            redirect("product/".$id,[
                "success" => "Sikeres művelet.",
            ]);
        }

        public static function getItems() {
            return self::$basket->getItems();
        }

        public static function getPieceSum() {
            $sum = 0;
            foreach (self::$basket->getItems() as $item) {
                $sum += $item->piece;
            }
            return $sum;
        }

        private static function saveBasket() {
            $_SESSION["basket"] = serialize(self::$basket);
        }
    }