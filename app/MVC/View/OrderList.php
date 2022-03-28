<?php
    namespace View;

    use Controller\SettingsController;
    
    class OrderList
    {
        private $theme;
        public function __construct($details)
        {
            SettingsController::getInstance();
            $this->theme = SettingsController::$theme;
            include __DIR__ . "/themes/" . $this->theme . "/orders/orders.html";
        }

        private function createOrders($details) {
            $index = 0;
            foreach($details as $order) {
                $index += 1;
                $id = $order['id'];
                $date = $order['date'];
                $price = $order['price'];
                $sign = $order['sign'];
                include __DIR__ . "/themes/" . $this->theme . "/orders/row.html";
            }
        }
    }
    