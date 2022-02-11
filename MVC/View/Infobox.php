<?php
    namespace View;

    use Controller\SettingsController;

    class Infobox
    {
        public function __construct($type,$message)
        {
            SettingsController::getInstance();
            $theme = SettingsController::$theme;

            switch ($type) {
                case 'error':
                    include __DIR__ . "/themes/" . $theme . "/infobox/error.html";
                    break;
                case 'success':
                    include __DIR__ . "/themes/" . $theme . "/infobox/success.html";
                    break;
                case 'warning':
                    include __DIR__ . "/themes/" . $theme . "/infobox/warning.html";
                    break;
                default:
                    break;
            }
        }
    }
    