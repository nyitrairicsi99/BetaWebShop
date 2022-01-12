<?php
    namespace Model;

    use Model\Image;

    class Gallery {

        public $images = array();
        public $first;

        public function __construct()
        {}

        public function addImage($id,$url) {
            array_push($this->images,new Image($id,$url));
            $this->first = $this->images[0];
        }

        public function getFirst() {
            return $this->images[0];
        }
    }