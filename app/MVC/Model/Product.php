<?php
    namespace Model;

    class Product {

        public $id;
        public $name;
        public $price;
        public $currency;
        public $gallery;
        public $url;
        public $description;
        public $category;
        public $stock;
        public $availablefrom;
        public $availableto;
        public $alwaysavailable;

        public function __construct($id,$name,$price,$currency,$gallery,$url,$description,$category = null,$stock = 0,$availablefrom = '1950-01-01 00:00:00',$availableto = '2050-01-01 00:00:00',$alwaysavailable = true)
        {
            $this->id = $id;
            $this->name = $name;
            $this->price = $price;
            $this->currency = $currency;
            $this->gallery = $gallery;
            $this->url = $GLOBALS['settings']['root_folder'] . "/" . $url;
            $this->description = $description;
            $this->category = $category;
            $this->stock = $stock;
            $this->availablefrom = $availablefrom;
            $this->availableto = $availableto;
            $this->alwaysavailable = $alwaysavailable;
        }

        public function setCategory($category) {
            $this->category = $category;
        }
    }