<?php
/**
 * This file handles the updating user details.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn()) { // if not logged in
    Redirect::to('index.php');
}

if(Input::exists()) { // if anything was posted
    if(Token::check(Input::get('token'))) { // if token was correct to prevent CSRF attacks
        $validate = new Validate(); // validate
        $validation = $validate->check($_POST, array(
            'name' => array(
                'required' => true,
                'min' => 2,
                'max' => 50
            )
        ));

        if($validate->passed()) { // if no validation errors
            try {
                $user->update(array( // update query
                    'name' => Input::get('name')
                ));

                // on index.php there will again be a flash message
                Session::flash('home', 'Your details have been updated.');
                Redirect::to('index.php');

            } catch(Exception $e) {
                die($e->getMessage());
            }
        } else { // show validation errors
            foreach($validate->errors() as $error) {
                echo $error, '<br>';
            }
        }
    }
}
?>

<form action="" method="post">
    <div class="field">
        <label for="name">Name</label>
        <input type="text" name="name" value="<?php echo escape($user->data()->name); ?>">

        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
        <input type="submit" value="Update">
    </div>
</form>