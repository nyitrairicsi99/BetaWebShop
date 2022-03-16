<?php
    /**
     * passwordsAcceptable
     *
     * @param  string $password1 
     * @param  string $password2
     * @return number 
     * Checks if the given 2 passwords are strong enought, and matches.
     * Error codes:
     * 0 - Everything correct
     * 1 - Not matches
     * 2 - Too short
     */

    function passwordsAcceptable($password1,$password2) {
        if ($password1==$password2) {
            if (strlen($password1)>=7) {
                return 0;
            } else {
                return 2;
            }
        } else {
            return 1;
        }
    }

    function hashPassword($password,$prefix = null,$suffix = null) {
        $prefix = $prefix==null ? $GLOBALS['settings']['pass_prefix'] : $prefix;
        $suffix = $suffix==null ? $GLOBALS['settings']['pass_suffix'] : $suffix;
        return password_hash($prefix.$password.$suffix, PASSWORD_DEFAULT);
    }

    function hashMatches($password,$hash) {
        $prefix = $GLOBALS['settings']['pass_prefix'];
        $suffix = $GLOBALS['settings']['pass_suffix'];
        return password_verify($prefix.$password.$suffix, $hash);
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }