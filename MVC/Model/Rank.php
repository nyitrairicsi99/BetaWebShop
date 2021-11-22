<?php
    namespace Model;

    class Rank {
        public $title = "";
        public $permissions = array();
        public function __construct($title,$permissions = [])
        {
            $this->title = $title;
            $this->permissions = $permissions;
        }

        public function addPermission($name) {
            array_push($this->permissions,$name);
        }

        public function hasPermission($name) {
            return in_array($name, $this->permissions);
        }
    }