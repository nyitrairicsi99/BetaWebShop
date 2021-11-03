<?php
    namespace Model;

    class NavbarItem {
        public $title = "";
        public $link = "";
        public $active = false;
        public function __construct($title,$link,$active)
        {
            $this->title = $title;
            $this->link = $link;
            $this->active = $active;
        }
    }