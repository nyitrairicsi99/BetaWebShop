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

    function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    function hashMatches($password,$hash) {
        return password_verify($password, $hash);
    }