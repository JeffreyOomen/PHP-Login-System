<?php
/**
 * This file is the starting point of the application.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
require_once 'core/init.php';

// When being redirected from the register page a message
// will be flashed saying registering was success.
if(Session::exists('home')) {
    echo '<p>' . Session::flash('home'). '</p>';
}

$user = new User(); // Create a new user object

if($user->isLoggedIn()) { // check if user is logged in
?>

    <p>Hello, <a href="profile.php?user=<?php echo escape($user->data()->username);?>"><?php echo escape($user->data()->username); ?></p>

    <ul>
        <li><a href="update.php">Update Profile</a></li>
        <li><a href="changepassword.php">Change Password</a></li>
        <li><a href="logout.php">Log out</a></li>
    </ul>
<?php

    if($user->hasPermission('admin')) {
        echo '<p>You are a Administrator!</p>';
    }

} else { // get this when not logged in
    Redirect::to("login.php");
}