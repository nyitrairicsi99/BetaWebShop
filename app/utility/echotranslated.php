<?php
    use Controller\LanguageController;
    
    function echotranslated($phrase){
        LanguageController::getInstance();
        echo LanguageController::translate($phrase);
    }

    function translate($phrase){
        LanguageController::getInstance();
        return LanguageController::translate($phrase);
    }