<?php
    namespace View;
    use Controller\SettingsController;

    class Basket
    {
        private $theme;

        public function __construct($basket)
        {
            SettingsController::getInstance();
            $this->theme = SettingsController::$theme;

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
    