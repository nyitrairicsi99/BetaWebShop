<?php
    //in PHP8 str_contains included by default, but in earlier versions we need this utiliy function
    function str_contains($str,$cont){
		if (strpos($str, $cont) !== false) {
			return true;
		} else {
			return false;
		}
	}