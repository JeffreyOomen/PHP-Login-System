<?php
/**
 * This class handles sessions.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class Session {
    /**
     * Simply checks whether a session with the given name exists.
     * @return true if exists, false otherwise.
     */
    public static function exists($name) {
        return (isset($_SESSION[$name])) ? true : false;
    }

    /**
     * This method will create a session variable with
     * the given key value pair.
     * @param $name the key of the session variable
     * @param $value the value of the session variable
     * @return mixed the session is being returned
     */
    public static function put($name, $value) {
        return $_SESSION[$name] = $value;
    }

    /**
     * This method will get a session with a certain key.
     * @param $name the name of the key of the session
     * @return mixed the session is being returned
     */
    public static function get($name) {
        return $_SESSION[$name];
    }

    /**
     * This method will delete a certain session.
     * @param $name the key name of the session
     */
    public static function delete($name) {
        if(self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * This method will 'flash' a message to the user by
     * getting the session, deleting it and return it again
     * so the message will disapear after a page reload.
     * @param $name the name of the key of the session
     * @param string $string the message which needs to be flashed
     * @return mixed the session if it did exist.
     */
    public static function flash ($name, $string = 'null') {
        if(self::exists($name)) {
            $session = self::get($name);
            self::delete($name);
                return $session;
        } else {
            self::put($name, $string);
        }
    }
}