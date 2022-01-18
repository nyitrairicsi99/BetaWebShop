<?php
    namespace Model;

    class BasketItem {
        public $id;
        public $piece;
        public function __construct($id,$piece)
        {
            $this->id = $id;
            $this->piece = $piece;
        }
    }