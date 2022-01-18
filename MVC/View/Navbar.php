<?php
    namespace View;

    use Controller\UserController;
    use Controller\SettingsController;
    use Controller\BasketController;

    class Navbar
    {
        private $structure = array();
        private $pageTitle;
        private $theme;

        public function __construct($pageTitle)
        {
            $this->pageTitle = $pageTitle;
            SettingsController::getInstance();
            $this->theme = SettingsController::$theme;

            UserController::getInstance();
        }

        public function create() {
            $webshopName = $this->pageTitle;
            include __DIR__ . "/themes/" . $this->theme . "/navbar/nav.html";
            
            include __DIR__ . "/themes/" . $this->theme . "/navbar/modals.html";
        }

        public function addItem($element) {
            array_push($this->structure,$element);
        }

        private function createItems() {
            foreach ($this->structure as $element) {
                if (get_class($element)=="Model\\NavbarItem") {
                    $activeStr = $element->active ? "active" : "";
                    $title = $element->title;
                    $link = $element->link;
                    include __DIR__ . "/themes/" . $this->theme . "/navbar/item.html";
                } else if (get_class($element)=="Model\\NavbarDropdown") {
                    $hasactive = false;
                    foreach($element->elements as $subelement) {
                        if ($subelement->active) {
                            $hasactive = true;
                        }
                    }
                    $title = $element->title;
                    $hasactive = $hasactive ? 'active' : '';
                    include __DIR__ . "/themes/" . $this->theme . "/navbar/dropdown.html";
                }
            }
        }

        private function createUserDropdown() {
            BasketController::getInstance();
            $countItems = BasketController::getPieceSum();
            $countText = $countItems == 0 ? '' : ('('.$countItems.')');
            if (UserController::$islogged && UserController::$loggedUser->rank->hasPermission('admin_access')) {
                include __DIR__ . "/themes/" . $this->theme . "/navbar/profile_admin.html";
            } else if (UserController::$islogged) {
                include __DIR__ . "/themes/" . $this->theme . "/navbar/profile.html";
            } else {
                include __DIR__ . "/themes/" . $this->theme . "/navbar/profile_notlogged.html";
            }
        }

        private function createDropdownItems($element) {
            foreach($element->elements as $element) {
                $title = $element->title;
                $link = $element->link;
                $active = $element->active ? 'active' : '';
                include __DIR__ . "/themes/" . $this->theme . "/navbar/dropdownrow.html";
            }
        }
    }
    