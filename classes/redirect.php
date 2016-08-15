<?php
/**
 * This class handles redirecting.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class Redirect {

    /**
     * This method will redirect the user to the given location.
     * @param null $location the location of a php script or a error number
     */
    public static function to($location = null) {
        if($location) {
            if(is_numeric($location)) { // if number redirect to error page
                switch($location) {
                    case 404:
                        header('HTTP/1.0 404 Not Found');
                        include 'includes/errors/404.php';
                        break;
                }
            }
            header('Location: '. $location);
            exit();
        }
    }
}