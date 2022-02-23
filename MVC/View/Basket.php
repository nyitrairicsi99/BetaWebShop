<?php
    namespace View;
    use Controller\SettingsController;
    use Controller\UserController;

    class Basket
    {
        private $theme;

        public function __construct($basket)
        {
            UserController::getInstance();
            SettingsController::getInstance();
            $this->theme = SettingsController::$theme;
            $loggedin = UserController::$islogged;
            include __DIR__ . "/themes/" . $this->theme . "/basket/basket.html";
        }

        private function createRows($basket) {
            $id = 0;
            foreach($basket->getItems() as $item) {
                $name = $item->product->name;
                $url = $item->product->url;
                $piece = $item->piece;
                $price = ($item->product->price * $piece).$item->product->currency->sign;
                include __DIR__ . "/themes/" . $this->theme . "/basket/row.html";
                $id += 1;
            }
        }
    }
    