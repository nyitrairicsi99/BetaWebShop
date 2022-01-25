<?php
    use Controller\LanguageController;
    
    function echotranslated($phrase){
        LanguageController::getInstance();
        echo LanguageController::translate($phrase);
    }