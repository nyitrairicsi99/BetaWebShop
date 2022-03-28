<?php
    namespace View;

    use Controller\SettingsController;
    use Controller\SearchController;
    
    class Search
    {
        public function __construct($category = "main")
        {
            SettingsController::getInstance();
            $theme = SettingsController::$theme;
            SearchController::getInstance();
            $value = SearchController::getSearchValue();
            include __DIR__ . "/themes/" . $theme . "/search/search.html";
        }
    }
    