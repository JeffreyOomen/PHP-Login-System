<?php
/**
 * This class contains sanitize methods for any input of the user
 * which may be dangerous.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
require_once 'core/init.php';

function escape($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}