<?php
    namespace View;

    use Controller\SettingsController;

    class Header
    {
        public function __construct($title)
        {
            SettingsController::getInstance();
            $theme = SettingsController::$theme;
            include __DIR__ . "/themes/" . $theme . "/header.html";
        }
    }
    