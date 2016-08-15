<?php
/**
 * This class handles Token functionality to secure
 * against Cross Site Request Forgery.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class Token {

    /**
     * This method will generate an unique token to prevent
     * Cross Site Request Forgery. Every time a page is loaded
     * where the user can input data and send it, a token will be
     * generated and be put in a session.
     * @return a session with key 'token'
     */
    public static function generate() {
        return Session::put(Config::get('sessions/token_name'), md5(uniqid()));
    }

    /**
     * This method will check if the request was valid. When te page was loaded
     * a token was generated and put in a session. Upon posting the form the
     * token will be also send. So the token from the session and the token being
     * send should be the same if the request is made by the same person.
     * @param $token an unique token being generated every page reload
     * @return bool true if token is valid, false otherwise
     */
    public static function check($token) {
        $tokenName = Config::get('sessions/token_name'); // will be 'token'

        // First there will be checked if a session with name 'token' even exists
        // and then if the tokens also match. If that's the case, the session with
        // 'token' should be deleted because next time there will be a new session with new token.
        if(Session::exists($tokenName) && $token === Session::get($tokenName)) {
            Session::delete($tokenName);
            return true;
        }

        return false;
    }
}