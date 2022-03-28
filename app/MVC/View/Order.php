<?php
    namespace View;

    use Controller\SettingsController;
    
    class Order
    {
        private $theme;
        public function __construct($details)
        {
            SettingsController::getInstance();
            $this->theme = SettingsController::$theme;
            $first_name = $details['first_name'];
            $last_name = $details['last_name'];
            $phone = $details['phone'];
            $postcode = $details['postcode'];
            $city = $details['city'];
            $street = $details['street'];
            $house_number = $details['house_number'];
            include __DIR__ . "/themes/" . $this->theme . "/order/orderdetails.html";
        }

        private function createPayTypeRow($details) {
            foreach($details['paytypes'] as $paytype) {
                $id = $paytype['id'];
                $type = $paytype['type'];
                include __DIR__ . "/themes/" . $this->theme . "/order/row.html";
            }
        }
    }
    