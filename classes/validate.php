<?php
/**
 * This class contains methods for validation of user input.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class Validate {
    private $_passed = false;
    private $_errors = array();
    private $_db = null;

    public function __construct() {
        $this->_db = DB::getInstance();
    }

    // This check method will check if any input fulfills the rules.
    // The $source can be a $_POST variable and the items an array like: 
    // array( 'username' => array('required' => true),
    //        'password' => array('required' => true))
    // So if we say $items as $item we get the array('required' => true)
    // and with this rules we take the $rule which is required and the 
    // $rule_value which is true. 
    public function check($source, $items = array()) {
        foreach($items as $item => $rules) {
            foreach($rules as $rule => $rule_value) {
                // The item here is for example username, so we get $_POST['username'] value which was entered.
                $value = $source[$item];
                $item = escape($item);

                if($rule === 'required' && empty($value)) {
                    $this->addError("{$item} is required");
                } else if (!empty($value)) {
                    switch($rule) {
                        case 'min': // check for minimum characters
                            if(strlen($value) < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters.");
                            }
                            break;
                        case 'max': // check for maximum characters
                            if(strlen($value) > $rule_value) {
                                $this->addError("{$item} must be a maximum of {$rule_value} characters.");
                            }
                            break;
                        case 'matches': // check if match (passwords for example while registering)
                            if($value != $source[$rule_value]) {
                                $this->addError("{$rule_value} must match {$item}.");
                            }
                            break;
                        case 'unique': // check if data is unique in the database
                            $check = $this->_db->get($rule_value, array($item, '=', $value));

                            if($check->count()) {
                                $this->addError("{$item} already exists.");
                            }
                            break;
                    }
                }
            }
        }

        if(empty($this->_errors)) {
            $this->_passed = true;
        }
    }

    /**
     * Will add an error the the errors array.
     * @param $error
     */
    private function addError($error) {
        $this->_errors[] = $error;
    }

    /**
     * Will give back the errors array.
     * @return array of errors.
     */
    public function errors() {
        return $this->_errors;
    }

    /**
     * @return bool true if validation is passed, false otherwise
     */
    public function passed() {
        return $this->_passed;
    }
}
