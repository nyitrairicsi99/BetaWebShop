<?php
    namespace Model;

    class User {
        public $id = null;
        public $username;
        public $email;
        public $rank;
        public $basket;
        public function __construct()
        {
        }

        public function hasPermission($perm) {
            if (isset($GLOBALS['settings']['superuser']) && $GLOBALS['settings']['superuser']==$this->username) {
                return true;
            } else {
                return $this->rank->hasPermission($perm);
            }
        }
    }