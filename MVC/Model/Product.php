<?php
    namespace Model;

    class Product {

        public $name;
        public $price;
        public $currency;
        public $gallery;

        public function __construct($name,$price,$currency,$gallery)
        {
            $this->name = $name;
            $this->price = $price;
            $this->currency = $currency;
            $this->gallery = $gallery;
        }
    }