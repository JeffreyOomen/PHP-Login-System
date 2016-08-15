<?php
/**
 * This class contains methods special for cookie
 * handling.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class Cookie {

    /**
     * This method will check if a cookie is set with
     * a certain key name.
     * @param $name the name of the cookie key
     * @return bool true if exists, false otherwise
     */
    public static function exists($name) {
        return (isset($_COOKIE[$name])) ? true : false;
    }

    /**
     * This method will get the cookie with the given key name.
     * @param $name the name of the cookie key
     * @return mixed the cookie with the given key name
     */
    public static function get($name) {
        return $_COOKIE[$name];
    }

    /**
     * This method will set a cookie in the browser with the
     * given name, value (which will be a unique combination)
     * and a time expiry which will be default 1 week (see config globals).
     * @param $name the name for the cookie
     * @param $value the value for the cookie
     * @param $expiry the expiry date for the cookie
     * @return bool If output exists prior to calling this function, setcookie() will
     * fail and return FALSE. If setcookie() successfully runs, it will return TRUE. This does not
     * indicate whether the user accepted the cookie.
     */
    public static function put($name, $value, $expiry) {
        if(setcookie($name, $value, time() + $expiry, '/')) {
            return true;
        }
        return false;
    }

    /**
     * This method will delete a cookie with a given key name.
     * @param $name the key name of the cookie which needs to be deleted.
     */
    public static function delete($name) {
        self::put($name, '', time() -1);
    }
}