<?php
    namespace Model;

    class User {
        public $id;
        public $username;
        public $email;
        public $rank;
        public $phone;
        public $firstname;
        public $lastname;
        public $address;
        public function __construct($username)
        {
            $this->username = $username;
        }
    }