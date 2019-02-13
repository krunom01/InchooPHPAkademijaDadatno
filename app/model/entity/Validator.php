<?php
/**
 * Created by PhpStorm.
 * User: kruno
 * Date: 08/02/19
 * Time: 14.36
 */

class Validator
{
    public $errors = [];

    public static function string($str)
    {
        if(empty($str) or preg_match('/[^a-z ćčžđš A-Z]+/', $str))
            {
                return false;
            } else{
            return true;
        }

    }

    public static function email($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            return true;
        }

    }
    public static function password($newPassword, $confirmPassword)
    {
        if(empty($newPassword) or empty($confirmPassword) or $newPassword != $confirmPassword or strlen($newPassword) < 6)
        {
            return false;
        } else{
            return true;
        }

    }
}