<?php
    function formatRoute($str) {
        $str = strlen($str)==0 ? "/" : $str;
        if ($str[strlen($str)-1]=="/") {
            $str = substr($str, 0, -1);
        }
        if (substr($str,0,1)!="/") {
            $str = "/".$str;
        }
        return $str;
    }