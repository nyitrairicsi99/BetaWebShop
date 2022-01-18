<?php
    namespace Model;

    class Basket {
        public $items = [];
        public function __construct()
        {
        }

        public function addItem($item) {
            array_push($this->items,$item);
        }

        public function getItems() {
            return $this->items;
        }
    }