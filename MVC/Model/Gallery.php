<?php
    namespace Model;

    class Gallery {

        public $images = array();
        public $first;

        public function __construct()
        {}

        public function addImage($url) {
            array_push($this->images,$url);
            $this->first = $this->images[0];
        }

        public function getFirst() {
            return $this->images[0];
        }
    }