<?php
/**
 * This file will handle a forgotten password.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
require_once 'core/init.php';

$user = new User();

if(Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'email' => array(
                'required' => true,
                'min' => 6
            )
        ));
    }

    if($validate->passed()) {
        if($userObj = $user->getDbInstance()->get('users', array('email', '=', Input::get('email')))->first()) {
            $userId = $userObj->id;
            $userEmail = $userObj->email;
            $randomPassword = $user->generatePassword(10);

            $salt = Hash::salt(32); // Generate an unique salt
            $user->update(array(
                'password' => Hash::make($randomPassword, $salt),
                'salt' => $salt
            ), $userId);


            $user->mailForgottenPassword($userEmail, $randomPassword);

            Session::flash('home', 'An email has been send with your new password!');
            Redirect::to('login.php');
        }
    } else {
        foreach($validate->errors() as $error) {
            echo $error, '<br>';
        }
    }
}
?>
<div class="container">

    <section>
        <div class="container_demo" >

            <div class="wrapper">
                <div id="forgotpass" class="animate form">
                    <form action="" method="post">
                        <h1>Password Recovery</h1>
                        <p>
                            <label for="email">Email Address</label>
                            <input type="text" name="email" id="email" value="<?php echo escape(Input::get('email')); ?>">
                        </p>

                        <input type="hidden" name="token" id="token" value="<?php echo escape(Token::generate()); ?>">

                        <p class="signin button">
                            <input type="submit" value="Reset Password">
                        </p>

                    </form>
                </div>
            </div>

        </div>
    </section>

</div>