<?php
/**
 * This file handles the user loggin in.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
require_once 'core/init.php';

$errors = null;
if(Input::exists()) { // if there was anything posted
    if(Token::check(Input::get('token'))) { // will check if CSRF token is correct
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'username' => array('required' => true),
            'password' => array('required' => true)
        ));

        if($validate->passed()) { // if validation succeeded
            $user = new User();

            // This is for the field to remember login credentials
            $remember = (Input::get('remember') === 'on') ? true : false;
            // Call the login method of user with username, password and remember (true / false).
            // The login() method will return true if login was success, false otherwise.
            $login = $user->login(Input::get('username'), Input::get('password'), $remember);

            if($login) {
                Redirect::to('index.php');
            } else {
                $errors[] = 'Incorrect username or password';
            }
        } else {
            $errors = $validate->errors();
        }
    }
}
?>

<div class="container">

    <section>
        <div class="container_demo" >

            <div class="wrapper">
                <div id="login" class="animate form">
                    <form  action="" autocomplete="on" method="post">
                        <h1>Log in</h1>
                        <?php
                            if ($errors != null) {
                                foreach ($errors as $error) { ?>
                                    <div class="alert alert-danger">
                                        <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $error; ?>
                                    </div>
                        <?php
                                }
                            }
                            if(Session::exists('home')) { ?>
                                <div class="alert alert-success">
                                    <i class="glyphicon glyphicon-ok"></i> &nbsp; <?php echo Session::flash('home'); ?>
                                </div>
                                <?php
                            }
                        ?>
                        <p>
                            <label for='username' class="uname" data-icon="u">Username</label>
                            <input id="username" name="username" required="required" type="text" placeholder="myusername or mymail@mail.com" value="<?php echo Input::get('username'); ?>">
                        </p>
                        <p>
                            <label for="password" class="youpasswd" data-icon="p"> Your password </label>
                            <input id="password" name="password" required="required" type="password" placeholder="eg. X8df!90EO" />
                        </p>
                        <p class="keeplogin">
                            <input type="checkbox" name="remember" id="loginkeeping" value="on" <?php if(Input::get('remember') == 'on') { echo 'checked = "checked"'; } ?> />
                            <label for="loginkeeping">Keep me logged in</label>
                        </p>
                        <p>
                            <a href="forgotpassword.php">Forgot password?</a>
                        </p>
                        <!-- here a token will be generated to prevent CSRF attacks -->
                        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
                        <p class="login button">
                            <input type="submit" value="Login" />
                        </p>
                        <p class="change_link">
                            Not a member yet ?
                            <a href="register.php" class="to_register">Join us</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>