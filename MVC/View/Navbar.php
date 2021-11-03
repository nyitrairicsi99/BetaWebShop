<?php
    namespace View;

    class Navbar
    {
        private $structure = array();

        public function __construct()
        {}

        public function create() {
            $webshopName = "Beta Webáruház";
            $theme = "default";
            include __DIR__ . "/themes/" . $theme . "/navbar/before.html";
            foreach ($this->structure as $element) {
                if (get_class($element)=="Model\\NavbarItem") {
                    $activeStr = $element->active ? "active" : "";
                    $title = $element->title;
                    $link = $element->link;
                    include __DIR__ . "/themes/" . $theme . "/navbar/item.html";
                } else if (get_class($element)=="Model\\NavbarDropdown") {
                    $title = $element->title;
                    include __DIR__ . "/themes/" . $theme . "/navbar/dropdownbefore.html";
                    foreach($element->elements as $element) {
                        $title = $element->title;
                        $link = $element->link;
                        include __DIR__ . "/themes/" . $theme . "/navbar/dropdownrow.html";
                    }
                    include __DIR__ . "/themes/" . $theme . "/navbar/dropdownafter.html";
                }
            }
            include __DIR__ . "/themes/" . $theme . "/navbar/after.html";
        }

        public function addItem($element) {
            /*if (get_class($element)=="Model\\NavbarItem") {
            } else if (get_class($element)=="Model\\NavbarDropdown") {
                $
            }*/
            array_push($this->structure,$element);
        }
    }
    