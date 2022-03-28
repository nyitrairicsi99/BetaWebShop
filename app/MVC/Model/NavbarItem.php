<?php
    namespace Model;

    class NavbarItem {
        public $title = "";
        public $link = "";
        public $active = false;
        public $icon = "";
        public function __construct($title,$link,$active,$icon = "fas fa-times")
        {
            $this->title = $title;
            $this->link = $GLOBALS['settings']['root_folder'] . "/" . $link;
            $this->active = $active;
            $this->icon = $icon;
        }
    }