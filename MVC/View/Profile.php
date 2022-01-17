<?php
    namespace View;

    use Controller\UserController;
    use Controller\SettingsController;

    class Profile
    {
        private $theme;

        public function __construct($details)
        {
            SettingsController::getInstance();
            $this->theme = SettingsController::$theme;

            UserController::getInstance();

            $username = $details['username'];
            $email = $details['email'];
            $first_name = $details['first_name'];
            $last_name = $details['last_name'];
            $phone = $details['phone'];
            $postcode = $details['postcode'];
            $city = $details['city'];
            $street  = $details['street'];
            $house_number  = $details['house_number'];

            include __DIR__ . "/themes/" . $this->theme . "/profile/profile.html";
        }
    }
    