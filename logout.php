<?php
/**
 * This file handles the user logging out.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
require_once 'core/init.php';

$user = new User();
$user->logout();

Redirect::to('index.php');