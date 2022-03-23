<?php
    namespace Model;

    class User {
        public $id = null;
        public $username;
        public $email;
        public $rank;
        public $twoFAPassed = true;
        
        public function __construct()
        {
        }

        public function hasPermission($perm) {
            if (!$this->twoFAPassed) {
                return false;
            }
            if (isset($GLOBALS['settings']['superuser']) && $GLOBALS['settings']['superuser']==$this->username) {
                return true;
            } else {
                return $this->rank->hasPermission($perm);
            }
        }
    }