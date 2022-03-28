<?php
    namespace Model;

    class Basket {
        public $items = [];
        public function __construct()
        {
        }

        public function addItem($item) {
            for ($i = 0; $i < count($this->items);$i++) {
                if ($this->getItem($i)->id==$item->id) {
                    $this->getItem($i)->piece = $this->getItem($i)->piece + $item->piece;
                    return;
                }
            }

            array_push($this->items,$item);
        }

        public function getItem($index) {
            if ($index<count($this->items) && $index>=0) {
                return $this->items[$index];
            } else {
                return null;
            }
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