<?php
/**
 * This class is used to make hashed passwords.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class Hash {

    /**
     * This method will generate a hash based on the given
     * parameters.
     * @param $string the plain password
     * @param string $salt the generated (see salt() function) salt
     * @return string the hashed password
     */
    public static function make($string, $salt = '') {
        return hash('sha256', $string . $salt);
    }

    /**
     * This method will generate an unique salt which is used
     * to encrypt a password.
     * @param $length the length of the salt
     * @return string a salt
     */
    public static function salt($length) {
        return mcrypt_create_iv($length);
    }

    /**
     * No idea.
     * @return string
     */
    public static function unique() {
        return self::make(uniqid());
    }
}