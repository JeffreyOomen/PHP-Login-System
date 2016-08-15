<?php
/**
 * This file handles the request to see the users profile
 * Created by Jeffrey Oomen on 12/08/2016.
 */
require_once 'core/init.php';

// Will get the $_GET['user'] value which was set in the url
// after clicking on profile from index.php
if(!$username = Input::get('user')) {
    Redirect::to('index.php');
} else {
    $user = new User($username);

    if(!$user->exists()) { // data should be fetched if the username was found in db, if not redirect to not found
        Redirect::to(404);
    } else { // if user data was found
        $data = $user->data();
?>

        <h3><?php echo escape($data->username); ?></h3> <!--show username-->
        <p>Name: <?php echo escape($data->name); ?></p> <!--show name-->

<?php
    }
}