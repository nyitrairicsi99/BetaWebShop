<?php
    namespace Model;

    class Currency {

        public $longName;
        public $shortName;
        public $sign;

        public function __construct($longName,$shortName,$sign)
        {
            $this->longName = $longName;
            $this->shortName = $shortName;
            $this->sign = $sign;
        }
    }