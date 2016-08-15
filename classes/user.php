<?php
/**
 * This class contains method to handle certain events
 * a user can do, for example logging in.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class User {
    private $_db,
            $_data, // will hold a db instance if record was found upon logging in of this user
            $_sessionName,
            $_cookieName,
            $isLoggedIn;

    /**
     * Whenever a new User is constructed, a database instance is obtained.
     * The session name and cookie name will be set with default values.
     * User constructor.
     * @param null $user
     */
    public function __construct($user = null) {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('sessions/session_name'); // will be 'user'
        $this->_cookieName = Config::get('remember/cookie_name'); // will be 'hash'

        if(!$user) { // if user is null
            if(Session::exists($this->_sessionName)) { //
                $user = Session::get($this->_sessionName);

                if($this->find($user)) {
                    $this->isLoggedIn = true;
                } else {
                    $this->isLoggedIn = false;
                }
            }
        } else { // will only find if user is NOT null
            $this->find($user);
        }
    }

    /**
     * This method will create a new user record in the database.
     * @param array $fields key-value pairs of columns and their values
     * @throws Exception if there was a problem
     */
    public function create($fields = array()) {
        if(!$this->_db->insert('users', $fields)) {
            throw new Exception('Sorry, there was a problem creating your account;');
        }
    }

    /**
     * This method will update certain fields, if the user is logged in.
     * @param array $fields the fields which needs to be updated in key-value pairs.
     * @param null $id
     * @throws Exception
     */
    public function update($fields = array(), $id = null) {

        if(!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }

        if(!$this->_db->update('users', $id, $fields)) {
            throw new Exception('There was a problem updating');
        }
    }

    /**
     * This method will find a certain user in the database if it exists.
     * @param null $user can be username or userId
     * @return bool true if record found, false otherwise.
     */
    public function find($user = null) {
        if($user) { // will be the username at first
            $field = (is_numeric($user)) ? 'id' : 'username';
            $data = $this->_db->get('users', array($field, '=', $user)); // this will return a db object

            if($data->count()) { // return the row count of the query
                $this->_data = $data->first(); // gets the first result in stdClass form (standard class)
                return true;
            }
        }
        return false;
    }

    /**
     * This method will take care of logging the user in.
     * @param null $username the username given
     * @param null $password the password given
     * @param bool $remember true if login session should be remember, false otherwise
     * @return bool true if logged in was success, false otherwise.
     */
    public function login($username = null, $password = null, $remember = false) {
        if(!$username && !$password && $this->exists()) {
            Session::put($this->_sessionName, $this->data()->id);
        } else {
            $user = $this->find($username); // return true if user was found, false otherwise

            if ($user) { // if user was found

                if ($this->data()->password === Hash::make($password, $this->data()->salt)) { // if passwords match
                    Session::put($this->_sessionName, $this->data()->id); // user : userID

                    // The users_session table will be ONLY used for remembering purposes.
                    if ($remember) { // if login credentials should be remembered
                        $hash = Hash::unique(); // create a unique hash for remembering cookie
                        // this will check if user already has an active session in the users_session table 
                        $hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));

                        if (!$hashCheck->count()) { // if not true, so if no results were found, do an insert
                            $this->_db->insert('users_session', array(
                                'user_id' => $this->data()->id,
                                'hash' => $hash
                            ));
                        } else {
                            $hash = $hashCheck->first()->hash; // otherwise get the already existing hash from the users_session table
                        }

                        // set a cookie to remember credentials
                        // cookieName = 'hash' and cookie_expiry is standard 604800 (one week) -> see init.php
                        Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                    }

                    return true;
                }
            }
        }
        return false;
    }

    /**
     * This method will check if the user has permission,
     * @param $key for example 'admin' or 'moderator'
     * @return bool true if has permission, false otherwise.
     */
    public function hasPermission($key) {
        $group = $this->_db->get('groups', array('id', '=', $this->data()->group));

        if($group->count()) {
            $permissions = json_decode($group->first()->permissions, true);

            return !empty($permissions[$key]); // return true if not empty
        }

        return false;
    }

    /**
     * Checks whether record data of this user object exists.
     * @return bool true if exists, false otherwise.
     */
    public function exists() {
        return (!empty($this->_data)) ? true : false;
    }

    /**
     * This method will log the user out by deleting the session and the cookie.
     */
    public function logout() {
        $this->_db->delete('users_session', array('user_id', '=', $this->data()->id));

        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
    }

    /**
     * Gets the data of the record of this user (which is a db object)
     * @return mixed a db object.
     */
    public function data(){
        return $this->_data;
    }

    /**
     * Checks whether the user is logged in.
     * @return bool true if logged in, false otherwise.
     */
    public function isLoggedIn() {
        return $this->isLoggedIn;
    }

    /**
     * This method will get the db instance.
     * @return DB|null db instance
     */
    public function getDbInstance() {
        return $this->_db;
    }

    /**
     * This method will generate a random sequence of
     * characters and numbers with the given length. It
     * is used in case the user has forgotten their password.
     * The password will be set to this random password so
     * the user can login again and change their password.
     * @param $length
     * @return string
     */
    public function generatePassword($length) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars),0,$length);
    }

    /**
     * This method will send an email to the given email address
     * with in the message the generated password by the generatePassword() method.
     * @param $email the email address to which the email should be send
     * @param $newPassword the plain generated password
     */
    public function mailForgottenPassword($email, $newPassword) {
        if (filter_var($email, FILTER_SANITIZE_EMAIL) && escape($newPassword)) {
            $message = "Your password has been reset. The new password is: " . $newPassword . "";
            $to = $email;
            $subject = "Forget Password";
            $from = 'info@loginsystem.com';
            $body='Hi, <br/> <br/>' . $message;
            $headers = "From: " . escape($from) . "\r\n";
            $headers .= "Reply-To: ". escape($from) . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            mail($to,$subject,$body,$headers);
        }
    }

    /**
     * Checks whether a certain email is already registered.
     * @param $email String email
     * @return bool true if already registered, false otherwise.
     */
    public function isAlreadyRegistered($email) {
        $this->_db->get("users", array("email", "=", $email));
        if ($this->_db->results()) {
            return true;
        }
        return false;
    }
}