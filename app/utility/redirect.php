<?php
    function redirect($url,$params = [],$nobreak = false){
        foreach ($params as $key => $value) {
            if ($key!='q') {
                $url .= "&".$key."=".$value;
            }
        }
        header('Location: '.$GLOBALS['settings']['root_folder'].'/'.$url);
        if (!$nobreak) {
            die();
        }
    }