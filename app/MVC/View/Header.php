<?php
    namespace View;

    use Controller\SettingsController;
    use Controller\AddonController;
    use View\Infobox;

    class Header
    {
        public function __construct($title)
        {
            SettingsController::getInstance();
            $theme = SettingsController::$theme;

            echo '<!DOCTYPE html><head>';
            include __DIR__ . "/themes/" . $theme . "/headercontent.html";
            
            AddonController::getInstance();
            echo AddonController::getHeaderTags();
            
            echo '</head>';

            $modalVars = ['error','success','warning'];
            foreach($modalVars as $var) {
                if (isset($_GET[$var])) {
                    new Infobox($var,$_GET[$var]);
                    break;
                }
            }
        }
    }
    