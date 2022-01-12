<?php
    namespace Model;

    class Image {

        public $url;
        public $id;

        public function __construct($id,$url)
        {
            $this->url = $url;
            $this->id = $id;
        }

    }