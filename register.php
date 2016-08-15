<?php
/**
 * This file handles the registering a new user.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
require_once 'core/init.php';

$errors = null;
if (Input::exists()) { // check if there was anything posted
    if(Token::check(Input::get('token'))) { // check if token was valid to prevent CSRF attack
        $validate = new Validate(); // validation
        $validation = $validate->check($_POST, array(
            'name' => array(
                'name' => 'Name',
                'required' => true,
                'min' => 2,
                'max' => 50
            ),
            'email' => array(
                'name' => 'Email',
                'required' => true,
                'min' => 10,
                'max' => 50
            ),
            'username' => array(
                'name' => 'Username',
                'required' => true,
                'min' => 2,
                'max' => 20,
                'unique' => 'users'
            ),
            'password' => array(
                'name' => 'Password',
                'required' => true,
                'min' => 6
            ),
            'password_confirmation' => array(
                'required' => true,
                'matches' => 'password'
            ),
        ));

        if ($validate->passed()) { // if validation succeeded
            $user = new User();

            if (!$user->isAlreadyRegistered(Input::get('email'))) {
                $salt = Hash::salt(32); // create a unique salt for password hashing

                try {
                    $user->create(array(
                        'name' => Input::get('name'),
                        'email' => Input::get('email'),
                        'username' => Input::get('username'),
                        'password' => Hash::make(Input::get('password'), $salt),
                        'salt' => $salt,
                        'joined' => date('Y-m-d H:i:s'),
                        'group' => 1
                    ));

                    // Make flash message to tell registering was success, will show up on index.php after redirect.
                    Session::flash('home', 'Welcome ' . Input::get('username') . '! Your account has been registered. You may now log in.');
                    Redirect::to('login.php');
                } catch(Exception $e) {
                    echo $error, '<br>';
                }
            } else {
                $errors[] = "A user with this email address already exists!";
            }
        } else { // show validation errors
            $errors = $validate->errors();
        }
    }
}
?>

<div class="container">

    <section>

        <div class="container_demo" >

            <div class="wrapper">

                <div id="register" class="animate form">

                    <form  action="" autocomplete="on" method="post">
                        <h1> Sign up </h1>
                        <?php
                            if ($errors != null) {
                                foreach ($errors as $error) { ?>
                                    <div class="alert alert-danger">
                                        <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $error; ?>
                                    </div>
                        <?php
                                }
                            }
                            ?>
                        <p>
                            <label for="namesignup" class="name" data-icon="u">Your name</label>
                            <input type="text" name="name" value="<?php echo escape(Input::get('name')); ?>" id="name" placeholder="James Bond" required="required">
                        </p>
                        <p>
                            <label for="usernamesignup" class="uname" data-icon="u">Your username</label>
                            <input type="text" name="username" id="username" value="<?php echo escape(Input::get('username')); ?>" required="required" placeholder="mysuperusername690">
                        </p>
                        <p>
                            <label for="emailsignup" class="youmail" data-icon="e" > Your email</label>
                            <input type="email" name="email" id="email" value="<?php echo escape(Input::get('email')); ?>" placeholder="mysupermail@mail.com" required="required">
                        </p>
                        <p>
                            <label for="passwordsignup" class="youpasswd" data-icon="p">Your password </label>
                            <input type="password" name="password" id="password" required="required" placeholder="eg. X8df!90EO">
                        </p>
                        <p>
                            <label for="passwordsignup_confirm" class="youpasswd" data-icon="p">Please confirm your password </label>
                            <input type="password" name="password_confirmation" id="password_again" value="" required="required" placeholder="eg. X8df!90EO">
                        </p>
                        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
                        <p class="signin button">
                            <input type="submit" value="Sign up"/>
                        </p>
                        <p class="change_link">
                            Already a member ?
                            <a href="login.php" class="to_register"> Go and log in </a>
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </section>

</div>