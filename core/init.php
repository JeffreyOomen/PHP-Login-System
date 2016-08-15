<?php
/**
 * This class is the core of every php file because
 * it contains information which every file needs.
 * By just including this file all classes needed
 * will be provided and session is started etc.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
session_start();

// Set the global array with values need throughout the application.
// The values of the keys will be used as session / cookie keys.
$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'db' => 'db'
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ),
    'sessions' => array(
        'session_name' => 'user',
        'token_name' => 'token'
    )
);

// If one of the domain classes are needed, these
// will be automatically loaded with this require statement.
spl_autoload_register(function($class) {
    require_once 'classes/' . $class . '.php';
});

require_once 'functions/sanitize.php';

// Whenever you have clicked 'remember me' while logging in a cookie will be send to the browser
// and will stay there for a week. This code beneath here will log the user automatically in when
// a cookie is detected and it contains a hashcode which will be compared with the on in the database
// in the table users_sessions. If there is found a result, the user will be logged in automatically
// even if the session is expired. In this way the user does not have to log in every time. 
// However, if the user logs out, the cookie is not valid anymore.
if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('sessions/session_name'))) {
    $hash = Cookie::get(Config::get('remember/cookie_name'));
    $hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));

    if($hashCheck->count()) {
        $user = new User($hashCheck->first()->user_id);
        $user->login();
    }
}
?>

    <link rel="stylesheet" type="text/css" href="./includes/css/demo.css" />
    <link rel="stylesheet" type="text/css" href="./includes/css/style.css" />
    <link rel="stylesheet" type="text/css" href="./includes/css/animate-custom.css" />

<?php
