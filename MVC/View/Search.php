<?php
    namespace View;

    use Controller\SettingsController;
    
    class Search
    {
        public function __construct()
        {
            SettingsController::getInstance();
            $theme = SettingsController::$theme;
            include __DIR__ . "/themes/" . $theme . "/search/search.html";
        }
    }
    