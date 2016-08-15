<?php
/**
 * This class is a helper class to ease the traversal
 * of the Config array in the core.init.php file.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class Config {

    /**
     * This is a helper method which will make traversing through
     * a nested array easier. In this case the Config array being
     * present in the core/init.php file. It is possible to access
     * the nested array in path-format so for example: remember/cookie_name
     * will access the remember array element and from this remember array
     * element the cookie_name element.
     * @param string|the $path the path to traverse the array
     * @return bool|mixed the value of the element being accessed or false if not found
     */
    public static function get($path = '') {
        if ($path && is_string($path)){
            $config = $GLOBALS['config'];
            $path = explode('/', $path);

            foreach($path as $bit) {
                if(isset($config[$bit])) {
                    $config = $config[$bit];
                }
            }

            return $config;
        }

        return false;
    }
}

// Here we get a path as argument, for example remember/cookie_name.
// This will be exploded, so it will become an array with each item
// is a part of the argument separated on the / sign. So $path[0] = 'remember'
// and $path[1] = 'cookie_name'. 

// Then we loop through each part of this $path array. So for the first item
// it will be checked if $GLOBALS has a key named 'remember', this is true,
// because it contains an array, so $config will be set with this value (an array).
// Then the second value will be looped through: 'cookie_name', it will also
// be checked if $config (which now contains the array and not the whole GLOBALS anymore)
// has a key named 'cookie_name' and it has, so  the value of $config will be replaced
// with this value of 'cookie_name' and will be returned. The value here would be: hash.