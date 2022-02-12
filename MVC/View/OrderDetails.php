<?php
    namespace View;

    use Controller\SettingsController;
    
    class OrderDetails
    {
        private $theme;
        public function __construct($details)
        {
            SettingsController::getInstance();
            $this->theme = SettingsController::$theme;
            $address = "";
            $address .= $details[0]['postcode'] . " ";
            $address .= $details[0]['city'] . " ";
            $address .= $details[0]['street'] . " ";
            $address .= $details[0]['house_number'];
            $date = $details[0]['order_time'];
            $paytype = $details[0]['pay_type'];
            $orderstate = $details[0]['order_state'];
            include __DIR__ . "/themes/" . $this->theme . "/orderdetails/orderdetails.html";
        }

        public function createOrders($orders) {
            $index = 0;
            foreach ($orders as $order) {
                $index += 1;
                $name = $order['name'];
                $price = $order['price'];
                $sign = $order['sign'];
                $piece = $order['piece'];
                include __DIR__ . "/themes/" . $this->theme . "/orderdetails/row.html";
            }
        }
    }
    