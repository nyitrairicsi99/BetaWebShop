<?php
    namespace View;

    class Search
    {
        public function __construct()
        {
            $theme = "default";
            include __DIR__ . "/themes/" . $theme . "/search/search.html";
        }
    }
    