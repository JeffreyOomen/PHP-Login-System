<?php
/**
 * This class contains method to deal with any input.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class Input {

    /**
     * This method will check if any input exists.
     * @param string $type post or get
     * @return bool true if input exists, false otherwise.
     */
    public static function exists($type = 'post') {
        switch($type) {
            case 'post':
                return (!empty($_POST)) ? true : false;
                break;
            case 'get':
                return (!empty($_GET)) ? true : false;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * This method will get the value of a posted
     * field if it's set. It will do for both POST
     * and GET.
     * @param $item the name of the POST or GET field
     * @return string the value of the field or nothing if nothing was set.
     */
    public static function get($item) {
        if(isset($_POST[$item])) {
            return $_POST[$item];
        } else if(isset($_GET[$item])) {
            return $_GET[$item];
        }

        return '';
    }
}