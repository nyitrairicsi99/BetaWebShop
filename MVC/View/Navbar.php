<?php
    namespace View;

    class Navbar
    {
        private $structure = array();
        private $pageTitle;

        public function __construct($pageTitle)
        {
            $this->pageTitle = $pageTitle;
        }

        public function create() {
            $webshopName = $this->pageTitle;
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
                        $active = $element->active;
                        include __DIR__ . "/themes/" . $theme . "/navbar/dropdownrow.html";
                    }
                    include __DIR__ . "/themes/" . $theme . "/navbar/dropdownafter.html";
                }
            }
            include __DIR__ . "/themes/" . $theme . "/navbar/after_menus.html";
            //include __DIR__ . "/themes/" . $theme . "/navbar/profile.html";
            include __DIR__ . "/themes/" . $theme . "/navbar/profile_admin.html";
            include __DIR__ . "/themes/" . $theme . "/navbar/after.html";
        }

        public function addItem($element) {
            array_push($this->structure,$element);
        }
    }
    