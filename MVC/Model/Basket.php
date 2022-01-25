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

        public function removeItem($index) {
            if (count($this->items)>$index) {
                array_splice($this->items, $index, 1);
            }
        }
    }