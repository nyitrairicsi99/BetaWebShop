<?php
    namespace Model;

    class Image {

        public $url;
        public $id;

        public function __construct($id,$url)
        {
            if (str_contains($url,'http://') || str_contains($url,'https://')) {
                $this->url = $url;
            } else {
                $this->url = $GLOBALS['settings']['root_folder']."/MVC/View/themes/default/src/images/upload/".$url;
            }
            $this->id = $id;
        }

    }