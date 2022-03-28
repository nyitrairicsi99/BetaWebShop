<?php
    namespace Model;

    class BasketItem {
        public $id;
        public $piece;
        public $product;
        
        public function __construct($id,$piece,$product = null)
        {
            $this->id = $id;
            $this->piece = $piece;
            $this->product = $product;
        }
    }