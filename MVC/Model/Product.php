<?php
    namespace Model;

    class Product {

        public $name;
        public $price;
        public $currency;
        public $gallery;
        public $url;
        public $description;

        public function __construct($name,$price,$currency,$gallery,$url,$description)
        {
            $this->name = $name;
            $this->price = $price;
            $this->currency = $currency;
            $this->gallery = $gallery;
            $this->url = $GLOBALS['settings']['root_folder'] . "/" . $url;
            $this->description = $description;
        }
    }