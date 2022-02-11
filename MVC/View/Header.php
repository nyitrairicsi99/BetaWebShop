<?php
    namespace View;

    use Controller\SettingsController;
    use View\Infobox;

    class Header
    {
        public function __construct($title)
        {
            SettingsController::getInstance();
            $theme = SettingsController::$theme;
            include __DIR__ . "/themes/" . $theme . "/header.html";

            $modalVars = ['error','success','warning'];
            foreach($modalVars as $var) {
                if (isset($_GET[$var])) {
                    new Infobox($var,$_GET[$var]);
                    break;
                }
            }
        }
    }
    