<?php
    namespace Model;

    class NavbarDropdown {
        public $title = "";
        public $elements = array();
        public function __construct($title,$elements)
        {
            $this->title = $title;
            $this->elements = $elements;
        }
    }