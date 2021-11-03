<?php
    namespace View;

    class Header
    {
        public function __construct($title)
        {
            $theme = "default";
            include __DIR__ . "/themes/" . $theme . "/header.html";
        }
    }
    